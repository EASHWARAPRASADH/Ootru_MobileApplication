<?php


namespace App\Traits;

use Yajra\DataTables\Services\DataTable;

trait DataTableTrait {

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax('')
            ->parameters($this->getBuilderParameters());
    }


    public function getBuilderParameters(): array
    {
        return [
            'lengthMenu'   => [[10, 50, 100, 500, -1], [10, 50, 100, 500, "All"]],
           'sDom'          => '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"d-flex" <"flex-grow-1" l><"p-2" i><"mt-4" p>><"clear">',
            'buttons' => [
                [
                    'extend' => 'print',
                    'text' => '<i class="fa fa-print"></i> Print',
                    'className' => 'btn btn-primary btn-sm',
                ],
                [
                    'extend' => 'csv',
                    'text' => '<i class="fa fa-file"></i> CSV',
                    'className' => 'btn btn-primary btn-sm',
                ]
            ],
            'drawCallback' => "function () {
                $('.dataTables_paginate > .pagination').addClass('justify-content-end mb-0');
                $('#dataTableBuilder th:first-child').removeClass('sorting_asc');
                if($('.image-popup-vertical-fit').length > 0){
                    $('.image-popup-vertical-fit').magnificPopup({
                        type: 'image',
                        closeOnContentClick: true,
                        closeBtnInside: false,
                        mainClass: 'mfp-with-zoom',
                        zoom: {
                            enabled: true,
                            duration: 350,
                        }
                    });
                }
            }",
            'language' => [
                'search' => '',
                'searchPlaceholder' => 'Search',
            ],
            'initComplete' => "function () {
                $('#dataTableBuilder_wrapper .dt-buttons button').removeClass('btn-secondary');
                this.api().columns().every(function () {

                });
            }",
            $darkModeEnabled = true,
            'createdRow' => "function (row, data, dataIndex, darkModeEnabled) {
                if (data.deleted_at) {
                    if(data.deleted_at != null){
                        let bgColor = '#ffe6e6';
                        if (darkModeEnabled) {
                            bgColor = '#ffe6e6';
                            $(row).css('color', '#000000');
                        }
                        $(row).css('background-color', bgColor);
                    }
                }
            }"
        ];
    }
}
