<?php
    return [
        'SIDEBAR' => [
            1 => [
                'icon' => 'fa fa-dashboard',
                'name' => 'Dashboard',
                'link' => '/dashboard',
                'allowd' => 1
            ],
            2 => [
                'icon' => 'fa fa-simplybuilt',
                'name' => 'Client Master',
                'link' => '#',
                'allowd' => 2,
                'child' => [
                    1 => [
                        'icon' => 'fa  fa-user',
                        'name' => 'Create Client',
                        'link' => '/client/create',
                        'allowd' => 3
                    ],
                    2 => [
                        'icon' => 'fa  fa-user-plus',
                        'name' => 'Create Consignee',
                        'link' => '/consignee/create',
                        'allowd' => 4
                    ],
                    3 => [
                        'icon' => 'fa  fa-users',
                        'name' => 'Consignee List',
                        'link' => '/consignee/list',
                        'allowd' => 5
                    ],
                    4 => [
                        'icon' => 'fa fa-users',
                        'name' => 'Client List',
                        'link' => '/client/list',
                        'allowd' => 6
                    ]
                ]
            ],   
            3 => [
                'icon' => 'fa fa-institution',
                'name' => 'Order To Collection',
                'link' => '#',
                'allowd' => 7,
                'child' => [
                    1 => ['icon' => 'fa fa-cart-arrow-down',
                        'name' => 'Internal Order',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
            
                            1 => [
                                'icon' => 'fa  fa-cart-arrow-down',
                                'name' => 'Create Internal Order',
                                'link' => '/internalorder',
                                'allowd' => 8
                            ],
                            2 => [
                                'icon' => 'fa fa-list-alt',
                                'name' => 'Internal Order List',
                                'link' => '/internal/list/open',
                                'allowd' => 9
                            ]
                        ]
                    ],
                    2 => ['icon' => 'fa fa-credit-card',
                        'name' => 'Job card',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1 => [
                                'icon' => 'fa fa-credit-card',
                                'name' => 'Create Job Card',
                                'link' => '/jobcard/create',
                                'allowd' => 10,
                            ],
                            2 => [
                                'icon' => 'fa fa-list-alt',
                                'name' => 'Job Card List',
                                'link' => '/JobCard/list/open',
                                'allowd' => 11,
                            ]
                        ]
                    ],
                    3 => ['icon' => 'fa fa-file-text-o',
                        'name' => 'Client P.O.',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1 => [
                                'icon' => 'fa  fa-file-text-o',
                                'name' => 'Create Client P.O.',
                                'link' => '/clientpo',
                                'allowd' => 12
                            ],
                            2=> [
                                'icon' => 'fa  fa-list-alt',
                                'name' => 'Client P.O. List',
                                'link' => '/clientpo/list',
                                'allowd' => 13
                            ]
        
                        ]
                    ],
                    4 => ['icon' => 'fa fa-upload',
                        'name' => 'Delivery Challan',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1=> [
                                'icon' => 'fa  fa-upload',
                                'name' => 'Create Delivery Challan',
                                'link' => '/deliverychallan',
                                'allowd' => 14
                            ],
                            2=> [
                                'icon' => 'fa  fa-list-alt',
                                'name' => 'Delivery Challan List',
                                'link' => '/deliverychallan/list',
                                'allowd' => 15
                            ]
                        ]
                    ],
                    5 => ['icon' => 'fa fa-file-text',
                        'name' => 'Tax Invoice',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1=> [
                                'icon' => 'fa fa-file-text',
                                'name' => 'Create Tax Invoice',
                                'link' => '/taxinvoice',
                                'allowd' => 16
                            ],
                            2=> [
                                'icon' => 'fa fa-list-alt',
                                'name' => 'Tax Invoice List',
                                'link' => '/taxinvoice/list',
                                'allowd' => 17
                            ],
                            3=> [
                                'icon' => 'fa fa-file-text',
                                'name' => 'Tax Invoice Dispatch',
                                'link' => '/taxdispatch',
                                'allowd' => 18
                            ],
                            4=> [
                                'icon' => 'fa fa-list-alt',
                                'name' => 'Tax Invoice Dispatch List',
                                'link' => '/taxinvoicedispatch/list',
                                'allowd' => 19
                            ]
        
                        ]
                    ],
                    6 => ['icon' => 'fa fa-file-text',
                        'name' => 'ASN/GRN',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1=> [
                                'icon' => 'fa fa-file-text',
                                'name' => 'Create ASN',
                                'link' => '/asn/create',
                                'allowd' => 20
                            ],
                            2=> [
                                'icon' => 'fa fa-file-text',
                                'name' => 'Create GRN',
                                'link' => '/grn/create',
                                'allowd' => 21
                            ]
                        ]
                    ],
                    7 => ['icon' => 'fa fa-file-text',
                        'name' => 'Waybill',
                        'link' => '#',
                        'allowd' => 7,
                        'child' => [
                            1=> [
                                'icon' => 'fa fa-file-text',
                                'name' => 'Way Bill List',
                                'link' => '/waybill/list',
                                'allowd' => 20
                            ],
                        ]
                    ]
                ],

            ],
            4 => [
                'icon' => 'fa fa-cog',
                'name' => 'Setting',
                'link' => '/settings',
                'allowd' => 22
            ],
            5 => [
                'icon' => 'fa fa-dashboard',
                'name' => 'Master List',
                'link' => '#',
                'allowd' => 23,
                'child' => [
                    1 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'HSN List',
                        'link' => '/hsn/list',
                        'allowd' => 24
                    ],
                    2 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'UOM List',
                        'link' => '/uom/list',
                        'allowd' => 25
                    ],
                    3 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'Payment Term List',
                        'link' => '/paymentterm/list',
                        'allowd' => 26
                    ],
                    4 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'Goods Invoice Dispatch List',
                        'link' => '/goodsdispatch/list',
                        'allowd' => 27
                    ],
                    
                ]
            ],
            6 =>[
                'icon' => 'fa fa-dashboard',
                'name' => 'User Management',
                'link' => '',
                'allowd' => 28,
                'child' => [
                    1 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'User Log',
                        'link' => '/user/log',
                        'allowd' => 29
                    ],
                    2 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'Create User',
                        'link' => '/user/create',
                        'allowd' => 30
                    ],
                    3 => [
                        'icon' => 'fa fa-list-alt',
                        'name' => 'Users List',
                        'link' => '/admin',
                        'allowd' => 30
                    ]
                ]
            ], 
            7 =>[
                'icon' => 'fa fa-dashboard',
                'name' => 'Reports',
                'link' => '#',
               
                'allowd' => 31,
                'child' => [
                    1 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'Tax Invoice Not Dispatched',
                        'link' => '#',
                        'allowd' => 32
                    ],
                    2 => [
                        'icon' => 'fa fa-dashboard',
                        'name' => 'Way Bill Nott Generated',
                        'link' => '/report/waybillnotgen/list',
                        'allowd' => 33
                    ]
                   
                ]
            ] 
                      
    ]
];
