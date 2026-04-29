<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Qr\QrCodeService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    private $view = 'admin.qr.';

    public function __construct(Public QrCodeService $qrCodeService)
    {}
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        try {
            $this->authorize('list_qr');
            $qrData = $this->qrCodeService->getAllQr();
            return view($this->view . 'index', compact('qrData'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|RedirectResponse|Response
     */
    public function create(): View|Factory|Response|RedirectResponse|Application
    {
        try {
            $this->authorize('create_qr');
            return view($this->view . 'create');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function store(Request $request): Response|RedirectResponse
    {
        try {
            $this->authorize('create_qr');

            $validatedData = $request->all();

           $this->qrCodeService->saveQrDetail($validatedData);
           return redirect()->route('admin.qr.index')->with('success',__('message.qr_add'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|RedirectResponse|Response
     */
    public function edit(int $id): View|Factory|Response|RedirectResponse|Application
    {
        try {
            $this->authorize('edit_qr');

            $qrData = $this->qrCodeService->findQrDetailById($id);
            return view($this->view . 'edit', compact('qrData'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function update(Request $request, int $id): Response|RedirectResponse
    {
        try {
            $this->authorize('edit_qr');

            $this->qrCodeService->updateQrDetail($request->all(), $id);
            return redirect()->route('admin.qr.index')->with('success',__('message.qr_update'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function delete(int $id): Response|RedirectResponse
    {
        try {
            $this->authorize('delete_qr');

            $this->qrCodeService->deleteQrDetail($id);
            return redirect()->route('admin.qr.index')->with('success',__('message.qr_delete'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function print(int $id): View|Factory|RedirectResponse|Application
    {
        try {
            $qrData = $this->qrCodeService->findQrDetailById($id);
            return view($this->view . 'print', compact('qrData'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
