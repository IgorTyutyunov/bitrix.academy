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
            'MESSAGE' => Loc::getMessage('ACADEMY_D7_UNISTALL_ERROR'),
            'DETAILS' => $ex->GetString(),
            'HTML' => true
        ]
    );
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage('ACADEMY_D7_UNINSTALL_TITLE'));
}
?>
<form action="<?=$APPLICATION->GetCurPage()?>" >
    <?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="academy.d7">
<input type="hidden" name="uninstall" value="Y">

<input type="hidden" name="step" value="2">
    <p>Вы можете сохрагить таблицы БД</p>
    <p>
        <input type="checkbox" name="savedata" id="savedata" checked value="Y">
        <label for="savedata">Сохранить таблицы БД</label>
    </p>
<input type="submit" name="" value="<?=Loc::getMessage('ACADEMY_D7_IS_UNINSTALL_MODULE')?>">
</form>
