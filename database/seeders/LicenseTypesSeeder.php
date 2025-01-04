<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenseTypesSeeder extends Seeder
{
    public function run()
    {
        // حذف البيانات القديمة
        DB::table('license_types')->truncate();

        // إضافة البيانات الجديدة
        $licenses = [
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات تسويق المنتجات الثلجية',
                'specialization' => '101010100',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات تسويق المنتجات الثلجية بالجملة',
                'specialization' => '101010200',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع معدات الصيد البحري',
                'specialization' => '101010300',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع معدات الصيد البحري بالجملة',
                'specialization' => '101010400',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الدرجات الهوائية والنارية',
                'specialization' => '101010500',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الدرجات الهوائية والنارية بالجملة',
                'specialization' => '101010800',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع لحوم الدواجن',
                'specialization' => '101010700',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الاسماك',
                'specialization' => '101010800',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الحلويات',
                'specialization' => '101010900',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع التحف والهدايا',
                'specialization' => '101011000',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات العناية بالجسم',
                'specialization' => '101011100',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات العناية بالجسم بالجملة',
                'specialization' => '101011200',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الطيور والحيوانات المنزلية',
                'specialization' => '101011300',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع الموازين وأجهزة القياس',
                'specialization' => '101011400',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع التوابل والبقوليات',
                'specialization' => '101011500',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع التوابل والبقوليات بالجملة',
                'specialization' => '101011600',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات الأم والطفل',
                'specialization' => '101011700',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات الأم والطفل بالجملة',
                'specialization' => '101011800',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات المخابز والحلويات',
                'specialization' => '101011900',
            ],
            [
                'category' => 'تجارة',
                'type' => 'رخص محلات بيع مستلزمات المخابز والحلويات بالجملة',
                'specialization' => '101012000',
            ],
        ];

        foreach ($licenses as $license) {
            DB::table('license_types')->insert($license);
        }
    }
}
