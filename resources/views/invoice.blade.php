<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factuur #{{ $invoice->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: "Roboto Mono", system-ui;
        }

        .col-span-15 {
            grid-column: span 18 / span 18;
        }

        .grid-cols-32 {
            grid-template-columns: repeat(32, minmax(0, 1fr));
        }

        .grid-cols-14 {
            grid-template-columns: repeat(14, minmax(0, 1fr));
        }

        @font-face {
            font-family: Roboto;
            src: url('../public/fonts/RobotoMono-VariableFont_wght.ttf');
        }
    </style>
</head>

<body class="h-full space-y-5 font-roboto">
    <main class="px-4 py-8 mx-auto max-w-7xl">
        <header class="flex flex-col items-center justify-between md:flex-row">
            <h1 class="text-4xl font-semibold print:text-3xl">Diederik Dezittere</h1>
            <div class="flex flex-col justify-center py-3 mt-4 text-xs print:text-[10px] text-right md:mt-0">
                <p>B.T.W Nr.: BE 636.299.412</p>
                <p>H.R.L 83.419 - H.R.H 55.17</p>
            </div>
        </header>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="text-sm print:text-xs">
                <ul class="font-semibold">
                    <li>Keulestraat 20</li>
                    <li>3390 Tielt (Brabant)</li>
                    <li>Tel. 016/63.34.58</li>
                    <li>GSM. 0475/23.69.17</li>
                    <li>Fax. 016/63.10.19</li>
                    <li>E-mail: diederik@dezittere.be</li>
                    <li>BIC: GEBABEBB</li>
                    <li>IBAN: BE04 2300 2703 5031</li>
                </ul>
            </div>
            <div class="flex items-center justify-start w-full h-full print:text-sm">
                <ul>
                    <li>{{ $invoice->client->name }}</li>
                    <li>{{ $invoice->client->address_one }}</li>
                    <li>{{ $invoice->client->address_two }}</li>
                    <li>{{ $invoice->client->address_three }}</li>
                </ul>
            </div>
        </section>

        <div class="space-y-4">
            <section class="grid grid-cols-1">
                <div class="py-3 text-center border-2 border-black">
                    <h1 class="text-2xl font-semibold print:text-xl">Factuur</h1>
                </div>
            </section>

            <div>
                <section class="grid grid-cols-3 text-sm border-t border-black print:text-xs border-x">
                    <div class="p-2 space-y-1 border border-black">
                        <p>Datum:</p>
                        <p class="font-semibold text-center">
                            {{ date_format(date_create($invoice->date ?? ''), 'd/m/Y') }}</p>
                    </div>
                    <div class="p-2 space-y-1 border border-black">
                        <p>Factuur Nr.:</p>
                        <p class="font-semibold text-center">{{ $invoice->number }}</p>
                    </div>
                    <div class="p-2 space-y-1 border border-black">
                        <p>BTW Nr. Klant:</p>
                        <p class="font-semibold text-center">{{ $invoice->btw }}</p>
                    </div>
                </section>

                <section class="grid grid-cols-32 text-xs print:text-[10px] border-black border-x">
                    <div class="p-2 space-y-1 text-center border border-black col-span-15">Omschrijving</div>
                    <div class="col-span-3 p-2 space-y-1 text-center border border-black">Aantal</div>
                    <div class="col-span-4 p-2 space-y-1 text-center border border-black">Prijs</div>
                    <div class="col-span-3 p-2 space-y-1 text-center border border-black">BTW %</div>
                    <div class="col-span-4 p-2 space-y-1 text-center border border-black">Bedrag</div>
                </section>

                <section class="flex flex-col text-[10px] print:text-[8px] border border-black">
                    @php
                        $total = 0;
                        $btw = 0;
                        $longer = 0;
                    @endphp
                    @foreach (json_decode($invoice->items) as $item)
                        @php
                            $total += $item->price * $item->amount;
                            $btw += !$invoice->btw ? 0 : (($item->price * $item->amount) / 100) * $item->btw;
                            $longer += (int) floor(strlen($item->description) / 80);
                        @endphp
                        <div class="grid items-center grid-cols-32">
                            <div class="p-1 space-y-1 border-black col-span-15 border-x">
                                <p>{{ $item->description }}</p>
                            </div>
                            <div class="col-span-3 p-1 space-y-1 text-right border-black border-x">
                                <p>{{ $item->amount }}/st/pc</p>
                            </div>
                            <div class="p-1 col-span-4 space-y-1 col-span-1.5 text-right border-black border-x">
                                <p>{{ number_format($item->price, 2, ',', '') }} EUR</p>
                            </div>
                            <div class="col-span-3 p-1 space-y-1 text-right border-black border-x">
                                <p>{{ number_format($item->btw, 2, ',', '') }}</p>
                            </div>
                            <div class="col-span-4 p-1 space-y-1 text-right border-black border-x">
                                <p>{{ number_format($item->price * $item->amount, 2, ',', '') }} EUR</p>
                            </div>
                        </div>
                    @endforeach
                    @for ($i = 0; $i < 24 - count(json_decode($invoice->items)); $i++)
                        <div class="grid h-5 grid-cols-32">
                            <div class="space-y-1 border-black col-span-15 border-x"></div>
                            <div class="col-span-3 border-black border-x"></div>
                            <div class="col-span-4 border-black border-x"></div>
                            <div class="col-span-3 border-black border-x"></div>
                            <div class="col-span-4 border-black border-x"></div>
                        </div>
                    @endfor
                </section>

                <section class="grid grid-cols-32 text-xs print:text-[10px] border border-t-0 border-black">
                    <div class="grid grid-cols-6 p-2 border border-black col-span-15">
                        @if (!$invoice->btw)
                            <div class="col-span-5">
                                <p>Leveringen onderworpen aan de bijzondere regeling van de heffing over de marge BTW
                                    niet aftrekbaar.</p>
                                <p>Livraison soumise au regime particulier d'imposition de la marge TVA non deductible.
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="col-span-7 p-2 text-center border border-black">
                        <p>Netto</p>
                        <p>BTW</p>
                        <p>Port</p>
                        <br />
                        <p class="text-sm font-semibold print:text-xs">Te betalen</p>
                    </div>
                    <div class="col-span-7 p-2 text-right border border-black">
                        <p>{{ number_format($total, 2, ',', '') }} EUR</p>
                        <p>{{ number_format($btw, 2, ',', '') }} EUR</p>
                        <p>{{ number_format($invoice->port, 2, ',', '') }} EUR</p>
                        <br />
                        <p class="text-sm font-semibold print:text-xs">
                            {{ number_format($total + $btw + $invoice->port, 2, ',', '') }} EUR</p>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>

</html>
