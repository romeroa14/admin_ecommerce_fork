<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Invoice;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_date')
                    ->label('Fecha de Factura')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('invoice_number')
                    ->label('Número de Factura')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Número copiado'),

                TextColumn::make('order.order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.orders.view', $record->order_id)),

                TextColumn::make('order.user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                

                

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->getStateUsing(function ($record) {
                        return current_currency_symbol() . ' ' . $record->total_amount;
                    })
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'warning' => 'overdue',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view_invoice')
                    ->label('Ver Factura')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Invoice $record) => route('invoices.view', $record))
                    ->openUrlInNewTab(),
                Action::make('download_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Invoice $record) => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
