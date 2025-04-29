 $interestPaidDate = DB::table('interest_calculations')
                ->select(['start_date', 'end_date'])
                ->first();

            if ($interestPaidDate) {
                if (
                    !($endDate < $interestPaidDate->start_date || $start_date > $interestPaidDate->end_date)
                ) {
                    return response()->json([
                        'status' => 'Fail',
                        'messages' => 'Interest Already Paid from ' . $post->date_from . ' To ' . $post->date_till_date
                    ]);
                }
            }


            $closing_balances = DB::table('member_accounts')
                ->leftJoin('member_savings', 'member_accounts.accountNo', '=', 'member_savings.accountNo')
                ->leftJoin('opening_accounts', 'member_accounts.accountNo', '=', 'opening_accounts.membershipno')
                ->leftJoin('ledger_masters', 'ledger_masters.reference_id', '=', 'opening_accounts.accounttype')
                ->where('ledger_masters.name', 'Saving')
                ->where('opening_accounts.membertype', $memberType)
                ->where('member_accounts.memberType', $memberType)
                ->where('member_savings.transactionDate', '<=', $endDate)
                ->select(
                    'member_accounts.accountNo as membershipnumber',
                    'member_accounts.name as customer_name',
                    'member_savings.accountId',
                    'opening_accounts.membershipno',
                    'opening_accounts.accountNo',
                    'opening_accounts.membertype',
                    'opening_accounts.accounttype',
                    'ledger_masters.reference_id',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                    'ledger_masters.name'
                )
                ->selectRaw(
                    'COALESCE(SUM(member_savings.depositAmount), 0) as totalDeposits,
                COALESCE(SUM(member_savings.withdrawAmount), 0) as totalWithdrawals'
                )
                ->groupBy(
                    'member_savings.accountId',
                    'member_accounts.accountNo',
                    'member_accounts.name',
                    'opening_accounts.membershipno',
                    'opening_accounts.accountNo',
                    'opening_accounts.membertype',
                    'opening_accounts.accounttype',
                    'ledger_masters.reference_id',
                    'ledger_masters.groupCode',
                    'ledger_masters.ledgerCode',
                    'ledger_masters.name'
                )
                ->get();


            $last_balances = [];
            $results = [];
            $amount = 0;

            foreach ($closing_balances as $balance) {
                $deposit_amount = $balance->totalDeposits;
                $withdraw_amount = $balance->totalWithdrawals;
                $last_balance = $deposit_amount ;

                $last_balances[$balance->accountId] = $last_balance;

                if (isset($minimum_amount) && $last_balance < $minimum_amount) {
                    continue;
                }

                $mainbalance = ($last_balance >= $minimum_amount) ? $last_balance : ($minimum_amount ?? 0);
                $amount = $deposit_amount - $withdraw_amount;

                $rate_of_intt = $post->rate_of_intt ?? 0; // Check if rate_of_intt exists
                $interest_amount = round(($mainbalance * $rate_of_intt) / 100 / 12);
                $net_amount = $amount + $interest_amount;




                if ($balance) {
                        if ($balance->groupCode && $balance->ledgerCode) {
                            $savingGroupCode = $balance->groupCode;
                            $savingLedgerCode = $balance->ledgerCode;
                        }
                    }
