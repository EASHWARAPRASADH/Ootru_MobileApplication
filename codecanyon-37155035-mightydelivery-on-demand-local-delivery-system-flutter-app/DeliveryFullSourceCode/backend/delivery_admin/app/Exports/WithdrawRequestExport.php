<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserBankAccount;
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

class WithdrawRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
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
        $query = WithdrawRequest::whereIn('user_id', $userIds)->with(['user', 'user.userBankAccount'])->where('status', 'requested');


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
                'bank_name' => $bankDetails->bank_name ?? '-',
                'bank_account_holder_name' => $bankDetails->account_holder_name ?? '-',
                'bank_account_number' => $bankDetails->account_number ?? '-',
                'bank_ifsc_code' => $bankDetails->bank_code ?? '-',
                'bank_address' => $bankDetails->bank_address ?? '-',
                'routing_number' => $bankDetails->routing_number ?? '-',
                'bank_iban' => $bankDetails->bank_iban ?? '-',
                'bank_swift' => $bankDetails->bank_swift ?? '-',
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
        if (in_array('bank_name', $this->selectedColumns)) {
            $row[] = $query['bank_name'] ?? '-';
        }
        if (in_array('bank_account_holder_name', $this->selectedColumns)) {
            $row[] = $query['bank_account_holder_name'] ?? '-';
        }
        if (in_array('bank_account_number', $this->selectedColumns)) {
            $row[] = $query['bank_account_number'] ?? '-';
        }
        if (in_array('bank_ifsc_code', $this->selectedColumns)) {
            $row[] = $query['bank_ifsc_code'] ?? '-';
        }
        if (in_array('bank_address', $this->selectedColumns)) {
            $row[] = $query['bank_address'] ?? '-';
        }
        if (in_array('routing_number', $this->selectedColumns)) {
            $row[] = $query['routing_number'] ?? '-';
        }
        if (in_array('bank_iban', $this->selectedColumns)) {
            $row[] = $query['bank_iban'] ?? '-';
        }
        if (in_array('bank_swift', $this->selectedColumns)) {
            $row[] = $query['bank_swift'] ?? '-';
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
            $headings[] = [__('message.pending_withdraw_request')];
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
                    case 'bank_name':
                        $columnHeadings[] = __('message.bank_name');
                        break;
                    case 'bank_account_holder_name':
                        $columnHeadings[] = __('message.bank_account_holder_name');
                        break;
                    case 'bank_account_number':
                        $columnHeadings[] = __('message.bank_account_number');
                        break;
                    case 'bank_ifsc_code':
                        $columnHeadings[] = __('message.bank_ifsc_code');
                        break;
                    case 'bank_address':
                        $columnHeadings[] = __('message.bank_address');
                        break;
                    case 'routing_number':
                        $columnHeadings[] = __('message.routing_number');
                        break;
                    case 'bank_iban':
                        $columnHeadings[] = __('message.bank_iban');
                        break;
                    case 'bank_swift':
                        $columnHeadings[] = __('message.bank_swift');
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
