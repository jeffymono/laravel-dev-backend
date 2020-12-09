<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql-ignug')->create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('authentication.users');
            $table->foreignId('state_id');
            $table->foreignId('teaching_ladder_id')->constrained('catalogues');
            $table->foreignId('dedication_time_id')->constrained('catalogues');
            $table->string('academic_unit')->nullable();
            $table->integer('tolta_subjects')->nullable();
            $table->integer('hours_worked')->comment('horas laboradas en la semana')->nullable();
            $table->integer('class_hours')->comment('horas clase en la semana')->nullable();
            $table->integer('investigation_hours')->comment('horas investigacion en la semana')->nullable();
            $table->integer('administrative_hours')->comment('horas administrativas en la semana')->nullable();
            $table->integer('community_hours')->comment('horas vinculacion en la semana')->nullable();
            $table->integer('other_hours')->comment('horas de otras actividades en la semana')->nullable();
            $table->boolean('technical')->comment('el docente da clases en carreras tecnicas')->default(false);
            $table->boolean('technology')->comment('el docente da clases en carreras tecnlogicas')->default(false);
            $table->boolean('sabbatical')->comment('el docente esta en periodo sabatico')->default(false);
            $table->date('sabbatical_start')->comment('fecha inicio periodo sabatico')->nullable();
            $table->foreignId('higher_education_id')->comment('el docente esta cursando estudios superiores escoger')->nullable()->constrained('catalogues');
            $table->string('institution_higher_education')->comment('nombre de la institucion de los estudios superiores')->default('NA');
            $table->foreignId('country_higher_education_id')->comment('pais de los estudios superiores')->nullable()->constrained('catalogues');
            $table->string('degree_higher_education')->comment('pais de los estudios superiores')->nullable();
            $table->foreignId('scholarship_id')->comment('posee beca')->nullable()->constrained('catalogues');
            $table->foreignId('scholarship_type_id')->comment('tipo de beca')->nullable()->constrained('catalogues');
            $table->double('scholarship_amount')->comment('monto de la beca')->default(0);
            $table->foreignId('financing_type_id')->comment('tipo de financiamiento de la beca')->nullable()->constrained('catalogues');
            $table->boolean('publications')->comment('tiene publicaciones en revistas indexadas')->default(false);
            $table->integer('total_publications')->comment('total publicaciones en revistas indexadas')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('pgsql-ignug')->dropIfExists('teachers');
    }
}
