<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateTenantUser implements ShouldQueue
{
    use Queueable;

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
        });
    }
}
