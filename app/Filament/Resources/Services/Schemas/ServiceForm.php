<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\FormHelpers;
use App\Models\Service;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Hizmet')
                    ->tabs([
                        Tab::make('İçerik')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Grid::make(2)->schema(FormHelpers::titleAndSlug('title', 'Hizmet adı', 'services')),
                                Select::make('group')
                                    ->label('Grup')
                                    ->options(Service::GROUPS)
                                    ->default(Service::GROUP_CATEGORY)
                                    ->required()
                                    ->selectablePlaceholder(false)
                                    ->helperText('Ürün kategorileri için her ilde ayrı sayfa açılır (/istanbul/toplu-telefon-alimi). Durum grupları yalnız genel sayfa olarak yayınlanır.'),
                                Textarea::make('short_description')
                                    ->label('Kısa açıklama')
                                    ->rows(2)
                                    ->maxLength(200)
                                    ->helperText('Ana sayfadaki hizmet kartında görünür.')
                                    ->columnSpanFull(),
                                Grid::make(2)->schema([
                                    FormHelpers::iconInput(),
                                    TextInput::make('image_alt')->label('Görsel ALT metni')->maxLength(150)->helperText('SEO ve erişilebilirlik için görseli tanımlayın.'),
                                ]),
                                FormHelpers::imageUpload('image', 'services', 'Hizmet görseli')->columnSpanFull(),
                                RichEditor::make('description')
                                    ->label('Hizmet açıklaması')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Detaylar')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                FormHelpers::simpleList('what_we_do', 'Neleri alıyoruz?'),
                                FormHelpers::simpleList('suitable_for', 'Kimlerden alıyoruz?'),
                                FormHelpers::stepsRepeater('process_steps', 'Çalışma süreci'),
                                FormHelpers::faqRepeater('faqs', 'Sık sorulan sorular'),
                            ]),

                        Tab::make('SEO & Yayın')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                FormHelpers::seoSection(),
                                Section::make('Yayın')->schema([
                                    Toggle::make('is_active')->label('Yayında')->default(true),
                                    Toggle::make('is_featured')->label('Ana sayfada göster')->default(true),
                                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                                ])->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
