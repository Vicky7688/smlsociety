use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompulsoryDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compulsory_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->string('type');
            $table->string('accno');
            $table->string('groupCode');
            $table->string('ledgerCode');
            $table->date('date');
            $table->double('Withdraw');
            $table->double('Deposit');
            $table->string('agent')->nullable();
            $table->double('Interest');
            $table->string('acc');
            $table->string('Bank');
            $table->string('narrartion');
            $table->enum('entry_mode', ['manual', 'automatic'])->default('manual');
            $table->string('ChqNo');
            $table->string('membertype');
            $table->string('admissionfee');
            $table->string('SessionYear');
            $table->string('Branch');
            $table->unsignedBigInteger('logged_branch')->default(0);
            $table->enum('is_delete', ['Yes', 'No'])->default('No');
            $table->string('DeletedBy')->nullable();
            $table->string('LoginId');
            $table->string('sessionId');
            $table->timestamp('created_at')->default(now());
            $table->timestamp('updated_at')->default(now());
            $table->string('bankname');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compulsory_deposits');
    }
}
