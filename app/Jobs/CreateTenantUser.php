<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Artisan;

class CreateTenantUser implements ShouldQueue
{
    use Queueable;

    protected Authenticatable $superAdmin;
    protected $superAdminRole = null;


    /**
     * Create a new job instance.
     */
    public function __construct(protected Tenant $tenant)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->tenant->run(function(){
            config(['cache.default' => 'array']);

            // Run the command
            Artisan::call('shield:generate', [
                '--all' => true,
                '--panel' => 'client',
            ]);
            User::create([
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
                'mobile_number' => $this->tenant->mobile_number,
                'landline_number' => $this->tenant->landline_number,
                'company_address' => $this->tenant->company_address,
                'company_owner_name' => $this->tenant->company_owner_name,
                'country_id' => $this->tenant->country_id,
                'state_id' => $this->tenant->state_id,
                'owner_email' => $this->tenant->owner_email,
                'company_logo' => $this->tenant->company_logo,
                'area_of_business' => $this->tenant->area_of_business,
            ]);
            $this->superAdmin = User::first();
            $this->superAdminRole = FilamentShield::createRole();
            $this->superAdmin
                ->unsetRelation('roles')
                ->unsetRelation('permissions');
            $this->superAdmin
            ->assignRole($this->superAdminRole);
        });
    }
}
