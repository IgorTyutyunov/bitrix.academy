function delRowInit()
{
    var ins_list = document.querySelectorAll('.my_prop .mp_row')

    //var parent_row = document.
    ins_list.forEach(function (item){
        var del_obj = item.querySelector('del');
        var abbr_obj = item.querySelector('abbr');
        if(!del_obj || abbr_obj)
        {
            return;
        }
        del_obj.onclick = delRowConfirm.bind(del_obj, item, abbr_obj);
        abbr_obj.onclick = delRowProcess.bind(item);
    })
}

function delRowConfirm(parent_row, abbr_obj)
{
    if(
        typeof this === 'undefined' || !this
        || typeof parent_row === 'undefined' || !parent_row
        || typeof abbr_obj === 'undefined' || !abbr_obj
    )
    {
        return;
    }

    parent_row.classList.toggle('confirming', true);
}

function delRowProcess()
{
    if(
        typeof this === 'undefined' || !this
    )
    {
        return;
    }

    this.parentNode.removeChild(this);
}

window.addEventListener('load', delRowInit);