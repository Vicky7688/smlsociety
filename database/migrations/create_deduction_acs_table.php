use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionAcsTable extends Migration
{
    public function up()
    {
        Schema::create('deduction_acs', function (Blueprint $table) {
            $table->id();
            $table->integer('account_no');
            $table->string('member_type');
            $table->string('account');
            $table->enum('type', ['saving', 'rd', 'loan']);
            $table->string('bankcode');
            $table->integer('deduction_date');
            $table->timestamps();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deduction_acs');
    }
}
