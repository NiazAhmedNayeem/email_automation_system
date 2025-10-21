<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers;
use App\Models\Client;
use App\Models\Offer;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Email Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->options(Property::all()->pluck('title', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->required()
                    ->seconds(false) 
                    ->native(false),
                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')->label('Client'),
                Tables\Columns\TextColumn::make('property.title')->label('Property'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'info' => 'send',
                        'warning' => 'pending',
                    ]),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime('d M Y, h:i A'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_email')
                    ->label('Send Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $template = \App\Models\EmailTemplate::first();
                        if (!$template) {
                            throw new \Exception('No email template found.');
                        }

                        \Mail::to($record->client->email)->send(
                            new \App\Mail\OfferEmail($record->client, $record->property, $template)
                        );

                        // Save email log
                        \App\Models\EmailLog::create([
                            'client_id' => $record->client_id,
                            'offer_id' => $record->id,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
