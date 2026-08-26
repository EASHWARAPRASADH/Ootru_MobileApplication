<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class WithdrawRequestapprovedExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $request;
    protected $counter;
    protected $status;
    protected $selectedColumns;


    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->counter = 1;
        $this->status = $request->input('status');
        $this->selectedColumns = $request->input('columns', []);
    }

    public function collection()
    {
        $authUser = auth()->user();
        $userIds = User::query()->pluck('id')->toArray();
        $query = WithdrawRequest::whereIn('user_id', $userIds)->with(['user', 'user.userBankAccount'])->where('status', 'approved');


        if ($authUser->user_type !== 'admin') {
            $query->where('user_id', $authUser->id);
        }
        $withdraws = $query->get();

        $data = $withdraws->map(function ($row) {
            $wallet = Wallet::where('user_id', $row->user_id)->first();
            $totalAmount = $wallet ? getPriceFormat($wallet->total_amount) : 0;
            $bankDetails = optional($row->user->userBankAccount);

            return [
                'id' => $row->id,
                'name' => optional($row->user)->name,
                'amount' => getPriceFormat($row->amount),
                'created_at' => dateAgoFormate($row->created_at),
                'updated_at' => dateAgoFormate($row->updated_at),
            ];
        })->toArray();

        return collect($data);
    }

    public function map($query): array
    {
        $row = [];

        if (in_array('id', $this->selectedColumns)) {
            $row[] = $query['id'] ?? '-';
        }
        if (in_array('name', $this->selectedColumns)) {
            $row[] = $query['name'] ?? '-';
        }
        if (in_array('amount', $this->selectedColumns)) {
            $row[] = $query['amount'] ?? '-';
        }
        if (in_array('created_at', $this->selectedColumns)) {
            $row[] = $query['created_at'] ? \Carbon\Carbon::parse($query['created_at'])->format('d-m-Y H:i:s') : '-';
        }
        if (in_array('approval_date', $this->selectedColumns)) {
            $row[] = $query['updated_at'] ? \Carbon\Carbon::parse($query['updated_at'])->format('d-m-Y H:i:s') : '-';
        }
       

        return $row;
    }

    public function headings($exportType = 'excel'): array
    {
        $headings = [];
        $fromDate = $this->request->input('from_date');
        $toDate = $this->request->input('to_date');
        $date = ($fromDate && $toDate) ? 'From Date: ' . ($fromDate ?: '-') . ' To Date: ' . ($toDate ?: '-') : null;

        if ($exportType === 'excel') {
            $headings[] = [__('message.approved_withdraw_request')];
            $headings[] = [];
            $columnHeadings = [];
            foreach ($this->selectedColumns as $column) {
                switch ($column) {
                    case 'id':
                        $columnHeadings[] = __('message.id');
                        break;
                    case 'name':
                        $columnHeadings[] = __('message.name');
                        break;
                    case 'amount':
                        $columnHeadings[] = __('message.amount');
                        break;
                    case 'created_at':
                        $columnHeadings[] = __('message.created_at');
                        break;
                    case 'approval_date':
                        $columnHeadings[] = __('message.approval_date');
                        break;
                }
            }

          

            $headings[] = $columnHeadings;
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:M1')->getStyle('A1:M1')->getFont()->setBold(true)->setSize(13);

        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F3:M3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


            $sheet->getStyle('A3:M3')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A:M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
