<html>
<head>
    <script>
        function OpenClose(obj) {
            if (obj.style.display == 'inline') {
                obj.style.display = 'none';
            } else {
                obj.style.display = 'inline';
            }
        }
    </script>
</head>

<body>

<center><img src='<{$xoops_url}>/modules/myquiz/images/qm.gif' border=0 alt=''></center>
<b>
    <{foreach item=quizz from=$block.objets}>
    <center><{$quizz.titre}></center>
    <b>
        <b>
            <{foreach item=c from=$block.ccvp}>
            <{if $c.pid == $quizz.idre}>

            <{if $c.vid == $quizz.ans}>
            <div id="ClickMe_Body" style="display:none;"> <img src='<{$xoops_url}>/modules/myquiz/images/ans.gif'
                                                               border=0 alt=''></div><{/if}><input name="radiobutton"
                                                                                                   type="radio"
                                                                                                   value="radiobutton"><{$c.cvpp}>
            <b><{/if}>
                <{/foreach}>
                <{/foreach}><b>

                    <div align="center" id="ClickMe_Head" onClick="OpenClose(document.getElementById('ClickMe_Body'))"
                         onMouseOver="this.style.cursor='pointer';">
                        <input type="submit" name="Cevap" value="<{$smarty.const._MYQUIZ_ANSWERME}>">
                    </div>

</body>
</html>










