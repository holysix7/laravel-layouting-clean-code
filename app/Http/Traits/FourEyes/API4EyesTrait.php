<?php

namespace App\Http\Traits\FourEyes;
use App\Services\Common\CommonTrait;
use Exception;

trait API4EyesTrait
{
    use CommonTrait;
    
    private $cConfigUrlKey = 'foureyes.url';

    public $cMenuAccessApproval = '4';
    public $cMenuAccessFormPenyesuaianDana = '13';
    public $cFlagApproved = '1';
    public $cFlagRejected = '2';

    private function request4EyesEngine(string $pMenuAccess, bool $pIsApproved, int $pRequestId, string $pNotes): array
    {
        $this->traceLog("[request4EyesEngine] start with menuAccess {$pMenuAccess}, isApproved {$pIsApproved}, " 
            . "requestId {$pRequestId}, and notes {$pNotes}");

        $user = session('user');
        $url = config($this->cConfigUrlKey, 'http://10.168.26.10:8088/4eyes-engine');
        $data = [
            "menu_access" => $pMenuAccess,
            "approval_action" => $pIsApproved ? '1' : '2',
            "request_id" => $pRequestId,
            "user_id" => $user->user_id,
            "name" => $user->name,
            "catatan" => $pNotes
        ];
        $tResult = sendAPIwToken($data, $url);
        
        $this->traceLog('[request4EyesEngine] end with result '.json_encode($tResult));
        return $tResult;
    }

    private function requestFormPenyesuaianDana(bool $pIsApproved, int $pRequestId, string $pNotes)
    {
        $this->traceLog("[requestFormPenyesuaianDana] start with isApproved {$pIsApproved}, " 
            . "requestId {$pRequestId}, and notes {$pNotes}");

        $tResult = $this->request4EyesEngine($this->cMenuAccessFormPenyesuaianDana, $pIsApproved, $pRequestId, $pNotes);

        $this->traceLog('[requestFormPenyesuaianDana] end with result ' . json_encode($tResult));

        if ($tResult['status'] != 200) {
            throw new Exception('Got not success result from foureyes engine approval.');
        }
    }

    private function requestActionPenyesuaianDana(bool $pIsApproved, int $pRequestId, string $pNotes)
    {
        $this->traceLog("[requestFormPenyesuaianDana] start with isApproved {$pIsApproved}, " 
            . "requestId {$pRequestId}, and notes {$pNotes}");

        $tResult = $this->request4EyesEngine($this->cMenuAccessApproval, $pIsApproved, $pRequestId, $pNotes);

        $this->traceLog('[requestFormPenyesuaianDana] end with result ' . json_encode($tResult));

        if ($tResult['status'] != 200) {
            throw new Exception('Got not success result from foureyes engine approval.');
        }
    }
}
