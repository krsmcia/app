<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $vendors = [
            [
                'code' => 'V001',
                'name' => '1ROTARY TRADING CORPORATION',
                'type' => 'supplier',
                'address' => 'PAJO 6015, LAPU LAPU CITY',
                'description' => 'AIRCON SUPPLIES AND TANKS',
            ],
            [
                'code' => 'V002',
                'name' => 'IMPERIAL APPLIANCE PLAZA',
                'type' => 'supplier',
                'phone' => '032 495 1808',
                'address' => 'ML QUEZON ST., CORNER MANTAWE RD., POBLACION, LAPU-LAPU CITY (OPON)',
            ],
            [
                'code' => 'V003',
                'name' => 'MR DIY',
                'type' => 'supplier',
                'address' => 'LG GARDEN WALK MACTAN LAPU LAPU CITY',
            ],
            [
                'code' => 'V004',
                'name' => 'BELMONT HARDWARE DEPOT',
                'type' => 'supplier',
                'address' => 'BRIONES ST HIGHWAY, MAGUIKAY, MANDAUE CITY',
                'description' => 'CONSTRUCTION AND MAINTENANCE SUPPLIES',
            ],
            [
                'code' => 'V005',
                'name' => 'SAVERS HOME DEPOT',
                'type' => 'supplier',
                'address' => 'MARIGONDON, LAPU LAPU CITY',
            ],
            [
                'code' => 'V006',
                'name' => 'ALLWOOD CONSTRUCTION SUPPLIES',
                'type' => 'supplier',
                'address' => 'BENEDICT VENTURE HERNAN CORTES ST MANDAUE CITY',
            ],
            [
                'code' => 'V007',
                'name' => 'MANDAUE ELECTRONIC CORP',
                'type' => 'supplier',
                'address' => 'MAGUIKAY, MANDAUE CITY',
            ],
            [
                'code' => 'V008',
                'name' => 'NUTECH',
                'type' => 'supplier',
                'address' => 'CEBU CITY',
            ],
            [
                'code' => 'V009',
                'name' => 'JOBER ELECTRONIC PARTS & ELECTRICAL SUPPLY',
                'type' => 'supplier',
                'phone' => '032 340 2231',
                'address' => 'RADAZA BLDG. POBLACION LAPU LAPU CITY',
                'description' => 'ELECTRICAL SUPPLIES',
            ],
            [
                'code' => 'V010',
                'name' => 'SUPER METRO',
                'type' => 'supplier',
            ],
            [
                'code' => 'V011',
                'name' => 'SHOPEE',
                'type' => 'supplier',
            ],
            [
                'code' => 'V012',
                'name' => 'MLR ENTERPRISES',
                'type' => 'supplier',
                'phone' => '0917 304 9423',
                'address' => 'SUBANGDAKU, MANDAUE CITY',
            ],
            [
                'code' => 'V013',
                'name' => 'ARTIFICE DIGITAL PRINTING SERVICES',
                'type' => 'supplier',
                'address' => 'CEBU CITY',
            ],
            [
                'code' => 'V014',
                'name' => 'CEBU HOME AND BUILDERS CENTRE - MACTAN',
                'type' => 'supplier',
                'address' => 'MENZI COMPOUND, M.L. QUEZON NATIONAL HIGHWAY, LAPU-LAPU CITY',
            ],
            [
                'code' => 'V015',
                'name' => 'SANITARY CARE PRODUCTS ASIA',
                'type' => 'supplier',
                'description' => 'JRT',
            ],
            [
                'code' => 'V016',
                'name' => 'MANDAUE FOAM PHILIPPINES',
                'type' => 'supplier',
                'address' => 'CUENCO AVENUE, CEBU CITY',
            ],
            [
                'code' => 'V017',
                'name' => 'CARBON MARKET',
                'type' => 'supplier',
            ],
            [
                'code' => 'V018',
                'name' => 'DAN ENRICO CORPORATION',
                'type' => 'supplier',
                'phone' => '0933 348 9903',
                'address' => 'PUROK PUSO POBLACION CORDOVA, CEBU',
            ],
            [
                'code' => 'V019',
                'name' => 'JML COMPUTER PARTS AND ACCESSORIES SHOP',
                'type' => 'supplier',
                'address' => 'MARIGONDON, LAPU LAPU CITY',
            ],
            [
                'code' => 'V020',
                'name' => 'WILCON DEPOT MANDAUE',
                'type' => 'supplier',
                'address' => 'OPAO MANDAUE CITY',
            ],
            [
                'code' => 'V021',
                'name' => 'NATIONAL BOOKSTORE',
                'type' => 'supplier',
                'address' => 'PUSOK LAPU-LAPU CITY',
            ],
            [
                'code' => 'V022',
                'name' => 'GP HOSE & ACU SUPPLY',
                'type' => 'supplier',
                'phone' => '09166830802',
                'address' => '1047 Hernan Cortes St, Mandaue, 6014 Cebu',
            ],
            [
                'code' => 'V023',
                'name' => 'MR DIY',
                'type' => 'supplier',
                'address' => 'LG GARDEN MACTAN',
            ],
            [
                'code' => 'V024',
                'name' => 'GAISANO GRANDMALL',
                'type' => 'supplier',
                'address' => 'MACTAN, LAPU LAPU CITY',
            ],
            [
                'code' => 'V025',
                'name' => 'MACTAN POOL',
                'type' => 'supplier',
                'address' => 'M.L Quezon National Highway, Bagumbayan, Maribago, Lapu-Lapu City',
            ],
            [
                'code' => 'V026',
                'name' => 'HEART STRING PH',
                'type' => 'supplier',
                'address' => 'MANDAUE CITY',
                'description' => 'SHOPEE ACCOUNT AND LOCAL',
            ],
            [
                'code' => 'V027',
                'name' => 'LAZADA',
                'type' => 'supplier',
            ],
            [
                'code' => 'V028',
                'name' => 'CEBU TRISTAR CORPORATION',
                'type' => 'supplier',
            ],
            [
                'code' => 'V029',
                'name' => 'RAPEDE TECHNICAL TESTING SERVICES',
                'type' => 'supplier',
                'phone' => '09167659970',
                'address' => 'Lawis, Jugan, Consolacion, Cebu, Philippines',
            ],
            [
                'code' => 'V030',
                'name' => 'PRYCE GASES, INC.',
                'type' => 'supplier',
                'phone' => '09918882342',
                'address' => 'BANGBANG, CORDOVA CEBU',
            ],
            [
                'code' => 'V031',
                'name' => 'NEW SAN',
                'type' => 'supplier',
            ],
            [
                'code' => 'V032',
                'name' => 'NEW',
                'type' => 'supplier',
            ],
            [
                'code' => 'V033',
                'name' => 'VISAYAN',
                'type' => 'supplier',
                'address' => 'MANDAUE',
            ],
            [
                'code' => 'V034',
                'name' => 'GJ COCOMART',
                'type' => 'supplier',
                'address' => 'MACTAN',
            ],
            [
                'code' => 'V035',
                'name' => 'IMPERIAL',
                'type' => 'supplier',
            ],
            [
                'code' => 'V036',
                'name' => 'MARN',
                'type' => 'supplier',
            ],
            [
                'code' => 'V037',
                'name' => 'MARNIKKO SCHOOL SUPPLIES',
                'type' => 'supplier',
            ],
            [
                'code' => 'V038',
                'name' => 'MARIAN KHY',
                'type' => 'supplier',
            ],
            [
                'code' => 'V039',
                'name' => 'AIRCON WASHBAG',
                'type' => 'supplier',
                'address' => 'MANILA, PHILIPPINES',
            ],
            [
                'code' => 'V040',
                'name' => 'INGCO',
                'type' => 'supplier',
                'address' => 'MACTAN',
            ],
            [
                'code' => 'V041',
                'name' => 'CVS FORTVIEW SYSTEMS CORP',
                'type' => 'supplier',
                'address' => 'HYE BUILDING, 292 A. DEL ROSARIO ST, MANDAUE CITY',
            ],
            [
                'code' => 'V042',
                'name' => 'JOYO',
                'type' => 'supplier',
                'address' => 'MACT6AN LG GARDEN WALK',
            ],
            [
                'code' => 'V043',
                'name' => 'MERCADO OPON',
                'type' => 'supplier',
                'address' => 'OPON',
            ],
            [
                'code' => 'V044',
                'name' => 'CHINA',
                'type' => 'supplier',
                'address' => 'CHINA',
                'description' => 'CV20',
            ],
            [
                'code' => 'V045',
                'name' => 'KOREA',
                'type' => 'supplier',
                'address' => 'KOREA',
            ],
            [
                'code' => 'V046',
                'name' => 'KOREA',
                'type' => 'customer',
            ],
            [
                'code' => 'V047',
                'name' => 'ENS TRADING CORP.',
                'type' => 'supplier',
                'phone' => '0999 220 5950',
                'address' => 'CORNER 2ND STREET, GUADALAJARA VILLAGE, GUADALUPE CEBU CITY',
                'description' => '3M AUTHORIZED DISTRIBUTOR',
            ],
            [
                'code' => 'V048',
                'name' => 'RG',
                'type' => 'supplier',
            ],
            [
                'code' => 'V049',
                'name' => 'CHINA',
                'type' => 'customer',
            ],
        ];
        $now = now();

        $vendors = array_map(function (array $vendor) use ($now) {
            return array_merge([
                'legal_name' => null,
                'contact_person' => null,
                'email' => null,
                'phone' => null,
                'website' => null,
                'address' => null,
                'tax_number' => null,
                'payment_terms' => null,
                'description' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], $vendor);
        }, $vendors);

        Vendor::insert($vendors);
    }
}
