<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Settings\GeneralSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class TripPdfController extends Controller
{
    /**
     * Stream a printable PDF of the trip's full detail page.
     */
    public function __invoke(Trip $trip, GeneralSettings $generalSettings): Response
    {
        abort_unless($trip->isPublished(), 404);

        $pdf = Pdf::loadView('frontend.trip-pdf', [
            'trip' => $trip,
            'generalSettings' => $generalSettings,
        ]);

        // Pages only exist once rendered, so page numbers must be added after render() but before stream().
        $pdf->render();

        $canvas = $pdf->getDomPDF()->getCanvas();
        $font = $pdf->getDomPDF()->getFontMetrics()->getFont('helvetica');

        $canvas->page_text($canvas->get_width() - 150, $canvas->get_height() - 30, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 8, [0.45, 0.45, 0.45]);

        return $pdf->stream("{$trip->slug}.pdf");
    }
}
