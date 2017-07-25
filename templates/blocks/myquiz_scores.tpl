<marquee scrollamount="2"
         direction="up" loop="true">
    <{foreach item=quizz from=$block.objets}>
    <b>Quizz: <{$quizz.titre}> </b><b>
        <{foreach item=p from=$block.perss}>
        <{if $p.ide == $quizz.idd}>
        <{$p.nom}>: <{$p.points}><b>
            <{/if}>
            <{/foreach}>
            <{/foreach}>
</marquee>
