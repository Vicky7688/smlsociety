<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0" />
                </svg>
                <img src="{{ asset('/assets/img/branding/logo.png') }}" alt="">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Page -->
        <li class="menu-item active">
            <a href="{{ route('/') }}" class="menu-link"><i class="ti ti-home"></i>
                <div data-i18n="Dashboard" class="ps-2">Dashboard</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle"><i class="ti ti-layout-list"></i>
                <div data-i18n="Masters" class="ps-2">Masters </div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                <li class="menu-item">
                    <a href="{{ route('sessionindex') }}" class="menu-link">
                        <div data-i18n="Session">Session</div>
                    </a>
                </li>
                {{-- <a href="{{ route('master', ['type' => 'branchMaster']) }}" class="menu-link">
                            <div data-i18n="Head Office">Head Office</div>
                        </a>
                   </li>

                <li class="menu-item">
                    <a href="{{ route('master', ['type' => 'borrowing']) }}" class="menu-link">
                        <div data-i18n="Borrowing Limit">Borrowing Limit</div>
                    </a>
                </li>
                 <li class="menu-item">
                        <a href="{{ route('master', ['type' => 'banners']) }}" class="menu-link">
                            <div data-i18n="Banners">Banners</div>
                        </a>
                   </li>


                   <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Employee Module">Employee Module</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('usersindex') }}" class="menu-link">
                                <div data-i18n="Job Profile">Job Profile</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('usersss') }}" class="menu-link">
                                <div data-i18n="Users">Users</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Address Module">Address Module</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'state']) }}" class="menu-link">
                                <div data-i18n="State">State</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'district']) }}" class="menu-link">
                                <div data-i18n="District">District</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'tehsil']) }}" class="menu-link">
                                <div data-i18n="Tehsil">Tehsil</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'postoffice']) }}" class="menu-link">
                                <div data-i18n="Post Office">Post office</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'village']) }}" class="menu-link">
                                <div data-i18n="Village">Village</div>
                            </a>
                        </li>
                    </ul>
                </li> --}}


                <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Accounting Module">Accounting Module</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('groupindex') }}" class="menu-link">
                                <div data-i18n="Create Group">Create Group </div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('ledgerindex') }}" class="menu-link">
                                <div data-i18n="Create Ledger">Create Ledger </div>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Schemes Module">Schemes Module</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('deposit-secheme-index') }}" class="menu-link">
                                <div data-i18n="Create Scheme">Create Scheme</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('FdTypeindex') }}" class="menu-link">
                                <div data-i18n="Create-Fd-Type">Create Fd Type</div>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Loan Module">Loan Module</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'loantypeMasters']) }}" class="menu-link">
                                <div data-i18n="Create Loan Type Master">Create Loan Type Master</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'loanMaster']) }}" class="menu-link">
                                <div data-i18n="Create Loan Master">Create Loan Master</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('master', ['type' => 'purposeMaster']) }}" class="menu-link">
                                <div data-i18n="Purpose Master">Purpose Master </div>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Daily Collection">Daily Collection</div>
                    </a>
                    <ul class="menu-sub">

                        <li class="menu-item">
                            <a href="{{route('agentindex')}}" class="menu-link">
                                <div data-i18n="Agents">Agents</div>
                            </a>
                        </li>

                    </ul>
                </li> --}}
                {{-- <li class="menu-item">
                    <a href="{{route('tds-index')}}" class="menu-link">
                        <div data-i18n="TDS">TDS</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('sodmasterindex')}}" class="menu-link">
                        <div data-i18n="Secured Over Draft(SOD)">Secured Over Draft(SOD)</div>
                    </a>
                </li> --}}
                {{-- <li class="menu-item">
                    <a href="{{route('bankfdmasterindex')}}" class="menu-link">
                        <div data-i18n="Bank FD Master">Bank FD Master</div>
                    </a>
                </li> --}}
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle"><i class="ti ti-book-2"></i>
                <div data-i18n="Transactions">Transactions</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('accountopen.page') }}" class="menu-link">
                        <div data-i18n="Membership Opening">Membership Opening</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{{ route('account-opening-index') }}" class="menu-link">
                        <div data-i18n="Account Opening">Account Opening</div>
                    </a>
                </li> --}}


                <li class="menu-item">
                    <a href="{{ route('share') }}" class="menu-link">
                        <div data-i18n="Share">Share</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{{route('savingaccountindex')}}" class="menu-link">
                        <div data-i18n="Saving">Saving</div>
                    </a>
                </li> --}}
                {{-- <li class="menu-item">
                    <a href="{{route('fdscheme.index')}}" class="menu-link">
                        <div data-i18n="Fixed Deposit  (Scheme)">Fixed Deposit (Scheme)</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('rd.recurring.index')}}" class="menu-link">
                        <div data-i18n="Recurring Deposit">Recurring Deposit</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('bankfdindex')}}" class="menu-link">
                        <div data-i18n="Bank Fixed Deposit">Bank Fixed Deposit</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('cclIndex')}}" class="menu-link">
                        <div data-i18n="CCL Advancement">CCL Advancement</div>
                    </a>
                </li> --}}

                <li class="menu-item">
                    <a href="{{ route('loan') }}" class="menu-link">
                        <div data-i18n="Loan">Loan</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('journalVoucher.index')}}" class="menu-link">
                        <div data-i18n="Journal Voucher">Journal Voucher</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{{route('dividend.index')}}" class="menu-link">
                        <div data-i18n="Dividend">Dividend</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item" style="">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Daily Collection">Daily Collection</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{route('dailysavingcollectionindex')}}" class="menu-link">
                                <div data-i18n="Daily Collection Saving">Daily Collection Saving</div>
                            </a>
                        </li>

                    </ul>
                </li> --}}
                {{-- <li class="menu-item">
                    <a href="{{route('agent-commission-index')}}" class="menu-link">
                        <div data-i18n="Agent Commission">Agent Commission</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('securityoncommissionIndex')}}" class="menu-link">
                        <div data-i18n="Security On Commission">Security On Commission</div>
                    </a>
                </li> --}}


            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle"><i class="ti ti-notebook"></i>
                <div data-i18n="Reports">Reports</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('generalLegderIndex') }}" class="menu-link">
                        <div data-i18n="General Ledger">General Ledger</div>
                    </a>
                </li>


                <li class="menu-item">
                    <a href="{{ route('receiptanddisbursementIndex') }}" class="menu-link">
                        <div data-i18n="Receipt & Disbursement">Receipt And Disbursement</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('daybookindex') }}" class="menu-link">
                        <div data-i18n="Day Book">Day Book</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('cashbookindex') }}" class="menu-link">
                        <div data-i18n="Cash Book">Cash Book</div>
                    </a>
                </li>



                {{-- <li class="menu-item">
                    <a href="{{ route('savingList.index') }}" class="menu-link">
                        <div data-i18n="Saving List">Saving List</div>
                    </a>
                </li> --}}




                <li class="menu-item">
                    <a href="{{ route('shareList.index') }}" class="menu-link">
                        <div data-i18n="Share List">Share List</div>
                    </a>
                </li>

                {{-- <li class="menu-item">
                    <a href="{{route('fdReport.index')}}" class="menu-link">
                        <div data-i18n="FD List">FD Report</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{route('rdReport.index')}}" class="menu-link">
                        <div data-i18n="RD List">RD Report</div>
                    </a>
                </li> --}}
                {{-- <li class="menu-item">
                    <a href="{{route('ccllistIndex')}}" class="menu-link">
                        <div data-i18n="SOD List">SOD List</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('sodledgerindexlist')}}" class="menu-link">
                        <div data-i18n="SOD Ledger">SOD Ledger</div>
                    </a>
                </li> --}}



                {{-- <li class="menu-item">
                    <a href="{{route('securitydepositlist')}}" class="menu-link">
                        <div data-i18n="Security On Comm. List">Security On Comm. List</div>
                    </a>
                </li> --}}



                <li class="menu-item">
                    <a href="{{ route('issueLoanReport.index') }}" class="menu-link">
                        <div data-i18n="Issue Loan List">Issue Loan Report</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{{route('dailyreportindex')}}" class="menu-link">
                        <div data-i18n="DailyCollection Report">DailyCollection Report</div>
                    </a>
                </li> --}}

                {{-- <li class="menu-item">
                    <a href="{{route('bankfdreportindex')}}" class="menu-link">
                        <div data-i18n="Bank FD Report">Bank FD Report</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('balancebookindex')}}" class="menu-link">
                        <div data-i18n="Balance Book">Balance Book</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('securitylistIndex')}}" class="menu-link">
                        <div data-i18n="Security Intt. Calculationt">Security Intt. Calculationt</div>
                    </a>
                </li> --}}


                {{-- <li class="menu-item">
                    <a href="{{route('interestcalculationindex')}}" class="menu-link">
                        <div data-i18n="Saving Intt. Calculation">Saving Intt. Calculation</div>
                    </a>
                </li> --}}

                <li class="menu-item">
                    <a href="{{ route('profitlossindex') }}" class="menu-link">
                        <div data-i18n="Profit&Loss Report">Profit&Loss Report</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('balancesheetindex') }}" class="menu-link">
                        <div data-i18n="Balance Sheet">Balance Sheet</div>
                    </a>
                </li>

            </ul>
        </li>
    </ul>

</aside>
