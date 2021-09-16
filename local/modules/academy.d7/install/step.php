<?
use Bitrix\Main\Localization\Loc;

if(!check_bitrix_sessid()) return;
global $APPLICATION;


//Если вдруг нужно будет сообщить об ошибке. Но это не D7. В D7 нужно использовать try catch, например try{// ...throw new Bitrix\Main\SystemException("Error");}catch (Bitrix\Main\SystemException $exception){echo $exception->getMessage();}
//$exception = new CApplicationException("File is not found", BX_E_FILE_NOT_FOUND);
//$APPLICATION->ThrowException($exception);

if($ex = $APPLICATION->GetException())
{
     CAdminMessage::ShowMessage(
        [
            'TYPE' => 'ERROR',
            'MESSAGE' => Loc::getMessage('ACADEMY_D7_ISTALL_ERROR'),
            'DETAILS' => $ex->GetString(),
            'HTML' => true
        ]
    );
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage('ACADEMY_D7_ISTALL_OK'));
}
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="submit" name="" value="<?=Loc::getMessage('ACADEMY_D7_MOD_BACK')?>">
</form>
