<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix faas_owners (It's a pivot table, we want it standalone)
        if (Schema::hasTable('faas_owners')) {
            Schema::dropIfExists('faas_owners');
        }

        Schema::create('faas_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_property_id');
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('faas_property_id', 'fk_faas_owners_property')
                  ->references('id')
                  ->on('faas_properties')
                  ->onDelete('cascade');
        });

        // 2. Ensure rpt_registration_owners is correct (it exists but let's be safe)
        if (!Schema::hasTable('rpt_registration_owners')) {
            Schema::create('rpt_registration_owners', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rpt_property_registration_id');
                $table->string('owner_name');
                $table->string('owner_tin')->nullable();
                $table->string('owner_address')->nullable();
                $table->string('owner_contact')->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->foreign('rpt_property_registration_id', 'fk_reg_owners_registration')
                      ->references('id')
                      ->on('rpt_property_registrations')
                      ->onDelete('cascade');
            });
        }

        // 3. Data Migration: Copy from legacy columns to new tables
        
        // From rpt_property_registrations
        $registrations = DB::table('rpt_property_registrations')
            ->whereNotNull('owner_name')
            ->where('owner_name', '!=', '')
            ->get();

        foreach ($registrations as $reg) {
            // Check if already exists to avoid duplicates if migration is re-run
            $exists = DB::table('rpt_registration_owners')
                ->where('rpt_property_registration_id', $reg->id)
                ->where('owner_name', $reg->owner_name)
                ->exists();

            if (!$exists) {
                DB::table('rpt_registration_owners')->insert([
                    'rpt_property_registration_id' => $reg->id,
                    'owner_name' => $reg->owner_name,
                    'owner_tin' => $reg->owner_tin ?? null,
                    'owner_address' => $reg->owner_address ?? null,
                    'owner_contact' => $reg->owner_contact ?? null,
                    'email' => $reg->owner_email ?? null,
                    'is_primary' => true,
                    'created_at' => $reg->created_at ?? now(),
                    'updated_at' => $reg->updated_at ?? now(),
                ]);
            }
        }

        // From faas_properties
        $properties = DB::table('faas_properties')
            ->whereNotNull('owner_name')
            ->where('owner_name', '!=', '')
            ->get();

        foreach ($properties as $prop) {
            $exists = DB::table('faas_owners')
                ->where('faas_property_id', $prop->id)
                ->where('owner_name', $prop->owner_name)
                ->exists();

            if (!$exists) {
                DB::table('faas_owners')->insert([
                    'faas_property_id' => $prop->id,
                    'owner_name' => $prop->owner_name,
                    'owner_tin' => $prop->owner_tin ?? null,
                    'owner_address' => $prop->owner_address ?? null,
                    'owner_contact' => $prop->owner_contact ?? null,
                    'is_primary' => true,
                    'created_at' => $prop->created_at ?? now(),
                    'updated_at' => $prop->updated_at ?? now(),
                ]);
            }
        }

        // 4. Cleanup: Remove legacy columns
        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_tin', 'owner_address', 'owner_contact', 'owner_email']);
        });

        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_tin', 'owner_address', 'owner_contact']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore legacy columns
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->string('owner_name')->nullable();
            $table->string('owner_tin')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
        });

        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->string('owner_name')->nullable();
            $table->string('owner_tin')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
            $table->string('owner_email')->nullable();
        });

        // Copy back data if possible (primary owner only)
        $owners = DB::table('faas_owners')->where('is_primary', true)->get();
        foreach ($owners as $owner) {
            DB::table('faas_properties')->where('id', $owner->faas_property_id)->update([
                'owner_name' => $owner->owner_name,
                'owner_tin' => $owner->owner_tin,
                'owner_address' => $owner->owner_address,
                'owner_contact' => $owner->owner_contact,
            ]);
        }

        $regOwners = DB::table('rpt_registration_owners')->where('is_primary', true)->get();
        foreach ($regOwners as $owner) {
            DB::table('rpt_property_registrations')->where('id', $owner->rpt_property_registration_id)->update([
                'owner_name' => $owner->owner_name,
                'owner_tin' => $owner->owner_tin,
                'owner_address' => $owner->owner_address,
                'owner_contact' => $owner->owner_contact,
                'owner_email' => $owner->email,
            ]);
        }

        // Drop standalone tables
        Schema::dropIfExists('faas_owners');
        Schema::dropIfExists('rpt_registration_owners');
        
        // Note: Re-creating the pivot faas_owners is not done here as it was likely a mistake, 
        // but if needed it would require knowing the previous schema exactly.
    }
};
