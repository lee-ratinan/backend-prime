<?php

/**
 * *********************************************************************
 * THIS MODEL IS SYSTEM MODEL, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 * Organization Master Model
 * @package App\Models
 */

namespace App\Models;

use CodeIgniter\Model;

class OrganizationMasterModel extends Model
{
    protected $table = 'organization_master';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'organization_name',
        'organization_address_1',
        'organization_address_2',
        'organization_address_3',
        'organization_address_country_code',
        'organization_address_postal_code',
        'organization_phone_country_calling_code',
        'organization_phone_number',
        'organization_email_address',
        'organization_website_url',
        'organization_social_links',
        'app_name',
        'trade_name',
        'registration_number',
        'incorporation_date',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    private array $configurations = [
        'id'                                => [
            'type'      => 'hidden',
            'label_key' => 'TablesOrganization.OrganizationMaster.id'
        ],
        'organization_name'                 => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_name',
            'required'    => true,
            'maxlength'   => 128,
            'placeholder' => 'Example Inc.',
            'details'     => 'TablesOrganization.OrganizationMaster.organization_name_details'
        ],
        'organization_address_1'            => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_address_1',
            'required'    => true,
            'placeholder' => '123 Main St.'
        ],
        'organization_address_2'            => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_address_2',
            'required'    => true,
            'placeholder' => 'New York'
        ],
        'organization_address_3'            => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_address_3',
            'required'    => false,
            'placeholder' => 'NY 00000'
        ],
        'organization_address_country_code' => [
            'type'        => 'select',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_address_country_code',
            'required'    => false,
            'placeholder' => 'US',
            'options'     => []
        ],
        'organization_address_postal_code'  => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_address_postal_code',
            'required'    => false,
            'placeholder' => '00000'
        ],
        'organization_phone'                => [
            'type'               => 'tel',
            'country_code_label' => 'TablesOrganization.OrganizationMaster.organization_phone_country_calling_code',
            'phone_number_label' => 'TablesOrganization.OrganizationMaster.organization_phone_number',
            'country_code_field' => 'organization_phone_country_calling_code',
            'phone_number_field' => 'organization_phone_number',
            'placeholder'        => '1234567890',
            'required'           => false
        ],
        'organization_email_address'        => [
            'type'        => 'email',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_email_address',
            'required'    => false,
            'placeholder' => 'john.doe@example.com'
        ],
        'organization_website_url'          => [
            'type'        => 'url',
            'label_key'   => 'TablesOrganization.OrganizationMaster.organization_website_url',
            'required'    => false,
            'placeholder' => 'https://example.com'
        ],
        'organization_social_links'         => [
            [
                'key'         => 'facebook',
                'type'        => 'url',
                'label_key'   => 'TablesOrganization.OrganizationMaster.social_platforms_available.facebook',
                'required'    => false,
                'placeholder' => 'https://facebook.com/example'
            ],
            [
                'key'         => 'linkedin',
                'type'        => 'url',
                'label_key'   => 'TablesOrganization.OrganizationMaster.social_platforms_available.linkedin',
                'required'    => false,
                'placeholder' => 'https://linkedin.com/in/example'
            ],
            [
                'key'         => 'x',
                'type'        => 'url',
                'label_key'   => 'TablesOrganization.OrganizationMaster.social_platforms_available.x',
                'required'    => false,
                'placeholder' => 'https://x.com/example'
            ],
            [
                'key'         => 'youtube',
                'type'        => 'url',
                'label_key'   => 'TablesOrganization.OrganizationMaster.social_platforms_available.youtube',
                'required'    => false,
                'placeholder' => 'https://youtube.com/example'
            ]
        ],
        'app_name'                          => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.app_name',
            'required'    => true,
            'placeholder' => 'Example Backoffice',
            'details'     => 'TablesOrganization.OrganizationMaster.app_name_details'
        ],
        'trade_name'                        => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.trade_name',
            'required'    => false,
            'placeholder' => 'Example Inc.',
            'details'     => 'TablesOrganization.OrganizationMaster.trade_name_details'
        ],
        'registration_number'               => [
            'type'        => 'text',
            'label_key'   => 'TablesOrganization.OrganizationMaster.registration_number',
            'required'    => false,
            'placeholder' => '1234567890'
        ],
        'incorporation_date'                => [
            'type'        => 'date',
            'label_key'   => 'TablesOrganization.OrganizationMaster.incorporation_date',
            'required'    => false,
            'placeholder' => 'YYYY-MM-DD'
        ],
        'created_by'                        => [
            'type'      => 'number',
            'label_key' => 'TablesOrganization.OrganizationMaster.created_by',
            'required'  => false
        ],
        'created_at'                        => [
            'type'      => 'datetime',
            'label_key' => 'TablesOrganization.OrganizationMaster.created_at',
            'required'  => false
        ],
        'updated_at'                        => [
            'type'      => 'datetime',
            'label_key' => 'TablesOrganization.OrganizationMaster.updated_at',
            'required'  => false
        ]
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @return array
     */
    public function getConfigurations(array $columns = []): array
    {
        $configurations = $this->configurations;
        // add country list
        $countries = lang('ListCountries.countries');
        $final_countries = array_map(function ($value) {
            return $value['common_name'];
        }, $countries);
        $configurations['organization_address_country_code']['options'] = $final_countries;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * Retrieve the only organization row
     * @return array
     */
    public function getOrganization(): array
    {
        return $this->where('id', ORGANIZATION_ID)->first();
    }
}