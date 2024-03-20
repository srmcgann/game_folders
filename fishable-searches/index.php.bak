<?php
  require('db.php');
  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $servers = [];
    for($i = 0; $i < mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      if($row['active']) $servers[] = $row;
    }
    $servers = json_encode($servers);
  }else{
    echo '[false]';
  }
?>
<DOCTYPE html>
<html>
  <head>
    <title>ARENA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      /* latin-ext */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
      }
      /* latin */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
      }
      body, html{
        margin: 0;
        background: #222;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        font-family: Courier Prime;
        font-size: 24px;
      }
      @media (orientation: landscape) {
        .pDiv{
          display: inline-block;
          width: 100%;
          height: calc(97% - 240px);
        }
        #returnButton{
          line-height: .9em;
          padding: 20px;
          min-width: 40px;
          min-height: unset;
          position: fixed;
          min-height: unset;
          right: 715px;
          top: -6px;
        }
        #mirrorsIframe{
          border: 0;
          margin: 0;
          width: 720px;
          height: 133vh;
          display: block;
          flat: left;
        }
        #practice{
          text-align: center;
          border: 0;
          margin: 0;
          vertical-align: top;
          background: linear-gradient(45deg, #000, #600);
          width: calc(133vw - 760px);
          display: block;
          color: #fff;
          padding: 20px;
          height: 133vh;
          float: left;
        }
        .practiceFrame{
          width: 100%;
          height: 100%;
          border: none;
          display: block;
        }
        hr{
          border: 1px solid #4f84;
          line-height: 0;
          margin: 0;
          padding: 0;
        }
        .logo{
          background-image: url(https://srmcgann.github.io/temp/burst.png);
          opacity: 100%;
          width: 150px;
          height: 150px;
          background-position: center center;
          background-size: 150px 150px;
          background-repeat: no-repeat;
          position: fixed;
          left: -10px;
          top: -10px;
        }
        .clear{
          clear: both;
        }
        #main{
          width: 133vw;
          height: 133vh;
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%) scale(.75, .75);
          position: fixed;
        }
      }

      @media (orientation: portrait) {
        .pDiv{
          display: inline-block;
            width: 100%;
            height: calc(97% - 240px);
        }
        #returnButton{
          line-height: .9em;
          padding: 20px;
          min-width: 40px;
          min-height: unset;
          position: fixed;
          right: -6px;
          top: -6px;
          animation-name: returnButton;
          animation-iteration-count: infinite;
        }
        #mirrorsIframe{
          border: 0;
          margin: 0;
          width: 133vw;
          height: 66vh;
          display: block;
          position: fixed;
          top: 66vh;
        }
        #practice{
          text-align: center;
          border: 0;
          margin: 0;
          vertical-align: top;
          padding: 20px;
          background: linear-gradient(45deg, #000, #600);
          width: calc(133vw - 40px);
          height: 66vh;
          display: block;
          color: #fff;
          height: 66vh;
        }
        .practiceFrame{
          width: 100%;
          height: 100%;
          border: none;
          display: block;
        }
        hr{
          border: 1px solid #4f84;
          line-height: 0;
          margin: 0;
          padding: 0;
        }
        .logo{
          background-image: url(https://srmcgann.github.io/temp/burst.png);
          opacity: 100%;
          width: 150px;
          height: 150px;
          background-position: center center;
          background-size: 150px 150px;
          background-repeat: no-repeat;
          position: fixed;
          left: -10px;
          top: -10px;
        }
        .clear{
          clear: both;
        }
        #main{
          transform: translate(-50%, -50%) scale(.75, .75);
          position: fixed;
          left: 50%;
          top: 50%;
          width: 133vw;
          height: 133vh;
        }
      }
      #practiceContainer{
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: space-evenly;
        flex-wrap: wrap;
        overflow: auto;
      }
      .title{
        width: calc(100% - 105px);
        float: right;
        text-align: left;
        padding-bottom: 10px;
        text-shadow: 2px 2px 2px #000;
      }
      .button:focus{
        outline: none;
      }
      .button{
        align-self: center;
        color: #0fa;
        background: #40cc;
        border: none;
        cursor: pointer;
        border-radius: 10px;
        font-size: 24px;
        padding: 5px;
        min-width: 100px;
        text-align: center;
        min-height: 360px;
        margin: 16px;
        min-width: 225px;
      }
      .gameThumbs{
        display: inline-block;
        width: 200px;
        height: 200px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
      }
      #tictactoeThumb{
        background-image: url(tictactoeThumb.jpg);
      }
      #sideToSideThumb{
        background-image: url(sideToSideThumb.png);
        background-color: #000;
      }
      #puyopuyoThumb{
        background-image: url(puyopuyoThumb.jpg);
        background-color: #000;
      }
      #battleracerThumb{
        background-image: url(battleracerThumb.jpg);
        background-color: #000;
      }
      #spelunkThumb{
        background-image: url(spelunkThumb.jpg);
        background-color: #000;
      }
      #trektrisThumb{
        background-image: url(trektrisThumb.jpg);
      }
      #orbsThumb{
        background-image: url(orbsThumb.jpg);
      }
      .captionContainer{
        display: block;
        margin: 5px;
        color: #fff;
        border-radius: 10px;
        width: 100%;
        font-size: 16px;
        max-width: calc(100% - 30px);
        padding: 10px;
        padding-top: 20px;
      }
      #backIcon{
        font-size: 3em;
        margin-top: 16px;
        display: inline-block;
      }
      #returnButton{
        animation-name: returnButton;
        animation-iteration-count: infinite;
        animation-duration: 2s;
        color: #fff;
        text-shadow: 2px 2px 2px #000;
        display: none;
      }
      .captionPractice{
        display: block;
        line-height: 2px;
      }
      .modal{
        width: 133vw;
        height: 133vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 2000;
        display: none;
        background: #123d;
        color: #fff;
        justify-content: space-evenly;
        flex-wrap: wrap;
        overflow: auto;
      }
      @keyframes returnButton {
        0%   {background-color: #184;}
        33%  {background-color: #148;}
        50%  {background-color: #841;}
        66%  {background-color: #418;}
        100%  {background-color: #184;}
      }
    </style>
  </head>
  <body>
    <div id="main">
      <div class="modal" id="buttonModal"></div>
      <div class="logo"></div>
      <div id="practice">
        <div class="title">
          <span style="font-size: 2em;">MULTIPLAYER</span>........<br>
          <center style="margin-left: -10%;font-size: 2em;">ARENA!</center><hr>
          practice here, then when you are ready,<br>
          create a game by clicking ARENA. There will be a link you can share, and the game will be joinable from a link that appears under "LIVE GAMES" here as well
          <button id="returnButton" class="button" onclick="returnToGames()">
            <span style="float: left;">back to<br>games </span>
            <span id="backIcon">⤾</span>
          </button>
          <br>
        </div>
        <div class="clear"></div>
        <div class="pdiv">
          <div id="practiceContainer"></div>
        </div>
      </div>
      <iframe id="mirrorsIframe" src="mirrors"></iframe>
    </div>
    <script>
      practiceContainer = document.querySelector('#practiceContainer')
      mirrorsIframe = document.querySelector('#mirrorsIframe')
      returnButton = document.querySelector('#returnButton')
      
      buttonModal = document.querySelector('#buttonModal')
      buttonModal.onclick = e => {
        e.stopPropagation()
        e.preventDefault()
        buttonModal.innerHTML = ''
        buttonModal.style.display = 'none'
      }
      
      shortWords=["aaron","aback","abase","abash","abate","abbey","abbot","abe","abeam","abed","abel","abet","abets","abhor","abide","able","ably","abode","abort","about","above","abuse","abut","abuts","abyss","ace","aces","ache","ached","aches","acid","acids","acme","acne","acorn","acre","acres","acrid","acryl","act","acted","actor","acts","acute","adage","adam","adams","adapt","add","added","adder","addle","adds","adept","adieu","adler","adman","admit","ado","adon","adopt","adore","adorn","adult","aegis","aesop","afar","affix","afire","afoot","afore","afoul","afro","aft","after","again","agana","agape","agate","age","aged","agent","ages","agile","aging","aglow","agnes","ago","agog","agony","agree","aha","ahead","ahem","ahoy","aid","aida","aide","aided","aider","aides","aids","ail","ailed","ails","aim","aimed","aims","air","aired","airs","airy","aisle","ajar","ajax","akin","alamo","alan","alarm","alas","album","alder","ale","alec","alert","ales","alex","algae","alias","alibi","alice","alien","align","alike","alit","alive","all","allah","allan","allay","alley","allot","allow","alloy","ally","alm","alms","aloft","aloha","alone","along","aloof","aloud","alp","alpha","alps","also","altar","alter","alto","altos","alum","alvin","amain","amass","amaze","amber","ambit","amble","ameba","amen","amend","amid","amine","amino","amiss","amity","amman","amok","among","amos","amour","amp","ample","amply","amps","amuck","amuse","amy","anal","and","andes","andre","andy","anew","angel","anger","angle","anglo","angry","angst","angus","anise","anita","ankle","ann","anna","anne","annex","annie","annoy","annul","annum","anode","anon","ant","ante","anti","antic","anton","ants","anus","anvil","any","aorta","apace","apart","ape","aped","apes","apex","aphid","apia","aping","apish","apple","apply","april","apron","apse","apt","aptly","aqua","arab","arabs","arbor","arc","arced","arch","arcs","arden","ardor","are","area","areas","arena","argh","argon","argos","argot","argue","argus","aria","arias","arid","aries","aril","arise","ark","arks","arm","armed","armor","arms","army","aroma","arose","array","arrow","arse","arson","art","arts","arty","aruba","arvin","aryan","ascii","ascot","ash","ashen","ashes","ashy","asia","asian","aside","ask","asked","asker","askew","asks","asp","aspen","aspic","asps","ass","assam","assay","asses","asset","aster","astir","ate","atilt","atlas","atoll","atom","atoms","atone","atop","attic","audio","audit","auger","aught","augur","auk","auks","auld","aunt","aunts","aura","aural","auras","auto","autos","avail","avant","aver","avers","avert","avery","avid","avoid","avon","avow","avows","await","awake","award","aware","awash","away","awe","awed","awes","awful","awing","awl","awls","awn","awoke","awol","awry","axed","axes","axial","axing","axiom","axis","axle","axles","aye","ayes","aztec","azure","baba","babe","babes","baby","bach","back","backs","bacon","bad","bade","badge","badly","bag","bagel","baggy","bags","bah","bail","bails","bait","baits","baize","bake","baked","baker","bakes","bald","bale","baler","bales","bali","balk","balks","balky","ball","balls","balm","balms","balmy","balsa","bambi","ban","banal","banco","band","bands","bandy","bane","banff","bang","bangs","banjo","bank","banks","banns","bans","bantu","bar","barb","barbs","bard","bards","bare","bared","bares","barge","bark","barks","barn","barns","baron","barry","bars","bart","basal","base","based","basel","baser","bases","bash","basic","basil","basin","basis","bask","basks","basle","basra","bass","bast","baste","bat","bata","batch","bate","bated","bates","bath","bathe","baths","baton","bats","batty","baud","baulk","bawd","bawds","bawdy","bawl","bawls","bay","bayou","bays","bbc","beach","bead","beads","beady","beak","beaks","beam","beams","bean","beans","bear","beard","bears","beast","beat","beats","beau","beaux","bebop","beck","bed","beds","bee","beech","beef","beefs","beefy","been","beep","beeps","beer","beers","bees","beet","beets","befit","befog","beg","began","beget","begin","begot","begs","begun","beige","being","belay","belch","belie","bell","belle","bello","bells","belly","below","belt","belts","ben","bench","bend","bends","benin","bent","beret","berg","bergs","bern","berry","bert","berth","beryl","beset","bess","best","bests","bet","beta","betel","beth","bets","betsy","betty","bevel","bevy","bias","bib","bible","bibs","bid","biddy","bide","bided","bides","bidet","bids","bier","biers","big","bight","bigot","bike","biked","bikes","bile","bilge","bill","bills","billy","bin","bind","binds","binge","bingo","bins","bios","biped","birch","bird","birds","birth","bison","bit","bite","biter","bites","bits","bitty","blab","blabs","black","blade","blake","blame","bland","blank","blare","blase","blast","blaze","bleak","blear","bleat","bled","bleed","bleep","blend","bless","blest","blew","blimp","blind","blink","blip","blips","bliss","blitz","bloat","blob","blobs","bloc","block","bloke","blond","blood","bloom","blot","blots","blow","blown","blows","blue","blues","bluff","blunt","blur","blurb","blurs","blurt","blush","boa","boar","board","boars","boas","boast","boat","boats","bob","bobby","bobs","bode","boded","bodes","bods","body","boer","boers","bog","bogey","boggy","bogie","bogs","bogus","bogy","boil","boils","bold","bolt","bolts","bomb","bombs","bond","bonds","bone","boned","boner","bones","bong","bongo","bonn","bonny","bonus","bony","boo","boob","boobs","booby","booed","book","books","boom","booms","boon","boons","boor","boors","boos","boost","boot","booth","boots","booty","booze","boozy","bop","borax","bore","bored","borer","bores","boris","born","borne","boron","bosh","bosom","boss","bossy","bosun","botch","both","bough","bound","bout","bouts","bow","bowed","bowel","bower","bowie","bowl","bowls","bows","box","boxed","boxer","boxes","boy","boyd","boys","bra","brace","brad","brads","brag","brags","braid","brain","brake","bran","brand","bras","brash","brass","brat","brats","brave","bravo","brawl","brawn","bray","brays","braze","bread","break","bream","bred","breed","brew","brews","brian","briar","bribe","brick","bride","brie","brief","brier","brig","brim","brims","brine","bring","brink","briny","brio","brisk","broad","broil","broke","bronx","brood","brook","broom","broth","brow","brown","brows","bruce","brunt","brush","brute","bryan","bsc","btu","buck","bucks","bud","buddy","budge","buds","buff","buffs","bug","buggy","bugle","bugs","build","built","bulb","bulbs","bulge","bulk","bulks","bulky","bull","bulls","bully","bum","bump","bumps","bumpy","bums","bun","bunch","bung","bungs","bunk","bunks","bunny","buns","buoy","buoys","burke","burly","burma","burn","burns","burnt","burp","burps","burr","burrs","burst","bury","bus","buses","bush","bushy","bust","busts","busy","but","butch","butt","butte","butts","buxom","buy","buyer","buys","buzz","bye","bylaw","byres","byron","byte","bytes","byway","cab","cabal","cabby","cabin","cable","cabs","cache","cacti","cad","caddy","cadet","cadge","cadre","cads","cafe","cafes","cage","caged","cages","cagey","cain","cairn","cairo","cake","caked","cakes","calf","call","calls","calm","calms","calve","cam","camas","came","camel","cameo","camp","camps","cams","can","can't","canal","candy","cane","caned","canes","cank","canna","canny","canoe","canon","cans","cant","canto","cants","cap","cape","caper","capes","capon","caps","car","carat","card","cards","care","cared","cares","carew","carey","cargo","carol","carp","carps","carry","cars","cart","carts","carve","case","cased","cases","cash","cask","casks","cast","caste","casts","cat","catch","cater","cathy","cats","catty","caulk","cause","cave","caved","caves","cavil","caw","cawed","caws","cbi","cease","cecil","cedar","cede","ceded","cedes","celia","cell","cello","cells","celt","celts","cent","cents","chad","chafe","chaff","chain","chair","chalk","champ","chant","chaos","chap","chaps","char","chard","charm","chars","chart","chary","chase","chasm","chat","chats","cheap","cheat","check","cheek","cheep","cheer","chef","chefs","chess","chest","chew","chews","chewy","chi","chic","chick","chide","chief","child","chile","chili","chill","chime","chimp","chin","china","chine","chink","chins","chip","chips","chirp","chit","chits","chive","chivy","chock","choir","choke","chomp","chop","chops","chord","chore","chose","chow","chows","chris","chubb","chuck","chuff","chug","chugs","chum","chump","chums","chunk","churl","churn","chute","cid","cider","cigar","cinch","cindy","circa","cite","cited","cites","city","civi","civic","civil","clack","clad","claim","clam","clamp","clams","clan","clang","clank","clans","clap","claps","clark","clash","clasp","class","claus","claw","claws","clay","clays","clean","clear","cleat","clef","clefs","cleft","clerk","click","cliff","climb","clime","cling","clink","clint","clip","clips","clive","cloak","clock","clod","clods","clog","clogs","clone","clonk","close","clot","cloth","clots","cloud","clout","clove","clown","cloy","cloys","club","clubs","cluck","clue","clues","clump","clung","coach","coal","coals","coast","coat","coats","coax","cob","cobol","cobra","cobs","coca","cock","cocks","cocky","cocoa","cod","coda","code","coded","codes","cog","cogs","cohen","coil","coils","coin","coins","coke","cokes","cola","cold","colds","cole","colic","colin","colne","colon","color","colt","colts","coma","comas","comb","combs","come","comer","comes","comet","comfy","comic","comma","con","conch","cone","coned","cones","coney","conga","congo","conic","conk","conks","cons","cony","coo","cooed","cook","cooks","cool","cools","coop","coops","coot","coots","cop","cope","coped","copes","cops","copse","copt","copts","copy","coral","cord","cords","core","cored","corer","cores","corey","corfu","corgi","cork","corks","corky","corn","corns","corny","corps","cos","cosec","cosh","cost","costs","cot","cots","couch","cough","could","count","coup","coupe","coups","court","cove","coven","cover","coves","covet","cow","cowed","cower","cowl","cowls","cows","cox","coy","coyly","coypu","cozy","crab","crabs","crack","craft","crag","crags","craig","cram","cramp","crams","crane","crank","crash","crass","crate","crave","craw","crawl","craws","craze","crazy","creak","cream","credo","creed","creek","creep","crepe","crept","cress","crest","crete","crew","crews","cri","crib","cribs","crick","cried","crier","cries","crime","crimp","crisp","croak","crock","crone","crony","crook","croon","crop","crops","cross","crow","crowd","crown","crows","crude","cruel","cruet","crumb","crush","crust","crux","cry","crypt","cub","cuba","cuban","cube","cubed","cubes","cubic","cubit","cubs","cud","cue","cued","cues","cuff","cuffs","cull","culls","cult","cults","cumin","cup","cupid","cups","cur","curae","curb","curbs","curd","curds","cure","cured","cures","curia","curie","curio","curl","curls","curly","curry","curs","curse","curst","curt","curve","cushy","cusp","cusps","cuss","cut","cute","cuter","cuts","cwt","cyan","cycle","cynic","cyril","cyst","cysts","czar","czars","czech","dab","dabs","dacca","dace","dacha","dad","daddy","dads","daffy","daft","daily","dairy","dais","daisy","dakar","dal","dale","dales","dally","dam","dame","dames","damn","damns","damp","damps","dams","dan","dance","dandy","dane","dank","danny","dante","dare","dared","dares","dark","darn","darns","dart","darts","dash","data","date","dated","dater","dates","datum","daub","daubs","daunt","dave","david","davis","davit","davy","dawn","dawns","day","days","daze","dazed","dazes","dazy","ddt","dead","deaf","deal","deals","dealt","dean","deans","dear","dears","death","deb","debar","debit","debra","debt","debts","debug","debut","decay","deck","decks","deco","decor","decoy","decry","deed","deeds","deem","deems","deep","deer","deere","defer","defog","deft","defy","degas","deify","deign","deism","deist","deity","delay","delft","delhi","dell","delta","delve","demi","demo","demon","demur","den","denim","denis","dens","dense","dent","dents","deny","depot","dept","depth","derek","desk","desks","deter","deuce","devil","dew","dewed","dewy","dfc","dhabi","dial","dials","diana","diane","diary","dice","diced","dices","dicey","dick","dicta","did","die","died","dies","diet","diets","dig","digit","digs","dijon","dike","dikes","dill","dim","dime","dimes","dimly","dims","din","dinar","dine","dined","diner","dines","ding","dingo","dings","dingy","dinky","dint","diode","dip","dips","dire","dirge","dirt","dirty","disc","disco","discs","dish","disk","disks","ditch","ditto","ditty","diva","divan","divas","dive","dived","diver","dives","dixie","dixon","dizzy","djinn","dna","doc","dock","docks","dodge","dodo","dodos","doe","doer","doers","does","doff","doffs","dog","doggy","dogma","dogs","doily","doing","dole","doled","doles","doll","dolls","dolly","dolt","dolts","dome","domed","domes","don","don't","done","donee","dong","doni","donna","donor","dons","doom","dooms","door","doors","dope","doped","doper","dopes","dopey","dora","doric","doris","dorm","dorsa","dory","dose","dosed","doses","doss","dot","dote","doted","dotes","dots","dotty","doubt","doug","dough","dour","douse","dove","dover","doves","dow","dowdy","dowel","dower","down","downs","downy","dowry","dowse","doyle","doze","dozed","dozen","dozes","dozy","drab","draft","drag","drags","drain","drake","dram","drama","drank","drape","drat","draw","drawl","drawn","draws","dray","dread","dream","dregs","dress","drew","dried","drier","dries","drift","drill","drily","drink","drip","drips","drive","droll","drone","drool","droop","drop","drops","dross","drove","drown","drub","drug","drugs","druid","drum","drums","drunk","dry","dryer","dryly","dsc","dual","dub","dubs","ducal","ducat","duchy","duck","ducks","duct","ducts","dud","dude","duds","due","duel","duels","dues","duet","duets","duff","dug","duke","dukes","dull","dully","duly","dumb","dummy","dump","dumps","dumpy","dun","dunce","dune","dunes","dung","dunk","dunks","duns","duo","dupe","duped","duper","dupes","duple","dusk","dusky","dust","dusts","dusty","dutch","duty","dwarf","dwell","dwelt","dye","dyed","dyer","dyers","dyes","dying","dyke","dykes","dylan","each","eager","eagle","ear","eared","earl","earls","early","earn","earns","ears","earth","ease","eased","easel","eases","east","easy","eat","eaten","eater","eaton","eats","eave","eaves","ebb","ebba","ebbed","ebbs","ebony","echo","eclat","ecru","edam","eddy","edema","eden","edgar","edge","edged","edges","edgy","edict","edify","edit","edith","edits","edna","educe","edwin","eel","eels","eerie","egg","egged","eggs","ego","egos","egret","egypt","eider","eight","eire","eject","eke","eked","ekes","eking","elan","eland","elate","elba","elbe","elbow","elder","elect","elegy","elf","elfin","eli","elias","elise","elite","elk","elks","ell","ella","ellen","elm","elms","elope","else","elsie","elude","elves","elvis","emacs","embed","ember","emend","emery","emil","emily","emir","emit","emits","emma","empty","emu","emus","enact","end","ended","endow","ends","endue","ene","enema","enemy","enid","enjoy","ennui","enoch","ens","ensue","enter","entry","envoy","envy","eon","eons","epee","epees","epic","epics","epoch","epoxy","epsom","equal","equip","era","eras","erase","erect","erg","ergo","ergs","eric","erie","erin","ernst","erode","eros","err","erred","errol","error","errs","erupt","esau","esc","ese","esker","espy","essay","esse","ester","estop","eta","etc","etch","ethel","ether","ethic","ethos","etna","etui","evade","evan","eve","even","evens","event","ever","every","evict","evil","evils","evoke","ewe","ewer","ewers","ewes","exact","exalt","exam","exams","excel","exe","exec","exert","exile","exist","exit","exits","expel","extol","extra","exude","exult","exxon","eye","eyed","eyes","eyre","ezra","faber","fable","face","faced","faces","facet","fact","facts","fad","faddy","fade","faded","fades","fads","fag","fagot","fags","fail","fails","fain","faint","fair","fairs","fairy","faith","fake","faked","faker","fakes","fakir","fall","falls","false","fame","famed","fan","fancy","fang","fangs","fanny","fans","far","farad","farce","fare","fared","fares","farm","farms","faro","faros","fast","fasts","fat","fatal","fate","fated","fates","fats","fatty","fault","fauna","faust","favor","fawn","fawns","fax","faxed","faxes","fay","faze","fear","fears","feast","feat","feats","fecal","feces","fed","fee","feed","feeds","feel","feels","fees","feet","feign","feint","fell","fells","felo","felon","felt","femur","fen","fence","fend","fends","fens","feral","fermi","fern","ferns","ferry","fetal","fetch","fete","feted","fetes","fetid","fetus","feud","feuds","fever","few","fewer","fey","fez","fiat","fiats","fib","fiber","fibs","fief","field","fiend","fiery","fife","fifes","fifth","fifty","fig","fight","figs","fiji","filch","file","filed","filer","files","filet","fill","fills","filly","film","films","filmy","filth","fin","final","finch","find","finds","fine","fined","finer","fines","finn","fins","fir","fire","fired","fires","firm","firms","firs","first","firth","fish","fishy","fist","fists","fit","fitch","fitly","fits","five","fives","fix","fixed","fixer","fixes","fizz","fizzy","fjord","flag","flags","flail","flair","flak","flake","flaky","flame","flan","flank","flap","flaps","flare","flash","flask","flat","flats","flaw","flaws","flax","flay","flays","flea","fleas","fleck","fled","flee","flees","fleet","flesh","flew","flex","flick","flier","flies","fling","flint","flip","flips","flirt","flit","flits","float","flock","floe","floes","flog","flogs","flood","floor","flop","flops","flora","floss","flour","flout","flow","flown","flows","flp","flu","flue","flues","fluff","fluid","fluke","flung","flunk","fluor","flush","flute","flux","fly","flyer","foal","foals","foam","foams","foamy","fob","fobs","focal","foci","focus","foe","foes","fog","fogey","foggy","fogs","fogy","foil","foils","foist","fold","folds","folio","folk","folks","folly","fond","font","fonts","food","foods","fool","fools","foot","fop","for","foray","force","ford","fords","fore","forge","forgo","fork","forks","form","forms","fort","forte","forth","forts","forty","forum","foul","fouls","found","fount","four","fours","fowl","fowls","fox","foxed","foxes","foxy","foyer","frail","frame","franc","frank","fraud","fray","frays","freak","fred","freda","free","freed","frees","freon","fresh","fret","frets","freud","friar","fried","fries","frill","frisk","frizz","fro","frock","frog","frogs","from","frond","front","frost","froth","frown","froze","frs","fruit","frump","fry","fudge","fuel","fuels","fugal","fugue","fuji","full","fully","fume","fumed","fumes","fun","fund","funds","fungi","funk","funks","funky","funny","fur","furl","furor","furry","furs","fury","furze","fuse","fused","fuses","fuss","fussy","fusty","futon","fuzz","fuzzy","gab","gable","gabon","gad","gael","gaffe","gag","gage","gages","gags","gail","gaily","gain","gains","gait","gaits","gal","gala","galas","gale","gales","gall","galls","gals","game","games","gamin","gamma","gamp","gamut","gang","gangs","gap","gape","gaped","gapes","gaps","garb","gary","gas","gases","gash","gasp","gasps","gassy","gate","gated","gates","gatt","gaudy","gauge","gaul","gaunt","gauss","gauze","gauzy","gave","gavel","gavin","gawk","gawks","gawky","gay","gays","gaze","gazed","gazer","gazes","gazon","gear","gears","gee","geese","gel","geld","gem","gems","gene","genes","genet","genie","genii","genoa","genre","genus","geoff","germ","germs","get","gets","getup","ghana","ghent","ghost","ghoul","giant","gibe","gibed","gibes","giddy","gift","gifts","gig","gigs","gild","gilds","gill","gills","gilt","gin","gins","gipsy","gird","girds","girl","girls","giro","giros","girth","gist","give","given","giver","gives","gizmo","glace","glad","glade","gland","glare","glass","glaze","gleam","glean","glee","glen","glens","glib","glide","glint","gloat","globe","gloms","gloom","glory","gloss","glove","glow","glows","glue","glued","glues","gluey","glum","glut","gluts","gmt","gnarl","gnash","gnat","gnats","gnaw","gnaws","gnome","gnu","gnus","goad","goads","goal","goals","goat","goats","gob","gobi","god","godly","gods","goes","gofer","going","gold","golf","golly","gonad","gone","goner","gong","gongs","goo","good","goods","goody","goof","goofs","goofy","goon","goose","gore","gored","gores","gorge","gorse","gory","gosh","got","goth","gouda","gouge","gourd","gout","gouty","gown","gowns","goya","grab","grabs","grace","grad","grade","graft","grail","grain","gram","grams","grand","grant","grape","graph","grasp","grass","grate","grave","gravy","gray","grays","graze","great","grebe","greed","greek","green","greet","grew","grid","grids","grief","grieg","grill","grim","grime","grimy","grin","grind","grins","grip","gripe","grips","grist","grit","grits","groan","groat","grog","groin","groom","grope","gross","group","grout","grove","grow","growl","grown","grows","grub","grubs","gruel","gruff","grump","grunt","guam","guano","guard","guava","guess","guest","guide","guild","guile","guilt","guise","gulf","gulfs","gull","gulls","gully","gulp","gulps","gum","gumbo","gummy","gums","gun","gunk","gunny","guns","guppy","guru","gurus","gus","gush","gushy","gust","gusto","gusts","gusty","gut","guts","gutsy","guy","guys","gym","gyms","gypsy","gyro","gyros","habet","habit","hack","hacks","had","hades","hag","hags","hague","haifa","haiku","hail","hails","hair","hairs","hairy","haiti","hake","hale","haley","half","hall","halls","halo","halos","halt","halts","halve","ham","hammy","hams","hand","hands","handy","hang","hangs","hank","hanks","hanky","hanna","hanoi","hanse","haply","happy","hard","hardy","hare","harem","hares","hark","harks","harm","harms","harp","harps","harpy","harry","harsh","hart","harts","has","hash","hasp","hasps","haste","hasty","hat","hatch","hate","hated","hater","hates","hats","haugh","haul","hauls","haunt","hausa","have","haven","haver","havoc","hawk","hawks","hawse","hay","haydn","hayed","hays","haze","hazel","hazes","hazy","he'd","he'll","he's","head","heads","heady","heal","heals","heap","heaps","hear","heard","hears","heart","heat","heath","heats","heave","heavy","heck","hedge","heed","heeds","heel","heels","heft","hefty","heinz","heir","heirs","heist","held","helen","helix","hell","hello","helm","helms","helot","help","helps","hem","hemp","hems","hen","hence","henry","hens","her","herb","herbs","herd","herds","here","hero","heron","hers","hertz","hess","hew","hewed","hewer","hewn","hews","hex","hey","hick","hid","hide","hides","hifi","high","highs","hike","hiked","hiker","hikes","hill","hills","hilly","hilt","hilts","him","hind","hindi","hinds","hindu","hinge","hint","hints","hip","hippy","hips","hire","hired","hirer","hires","his","hiss","hit","hitch","hits","hive","hived","hives","hmos","hoard","hoary","hoax","hob","hobby","hobo","hobs","hoc","hock","hocks","hod","hods","hoe","hoed","hoes","hog","hogs","hoist","hold","holds","hole","holed","holes","holly","holm","holst","holy","home","homed","homer","homes","homo","honda","honey","honk","honks","honor","hooch","hood","hoods","hooey","hoof","hoofs","hook","hooks","hooky","hoop","hoops","hoot","hoots","hop","hope","hoped","hopes","hops","horde","horn","horns","horny","horse","hose","hosea","hosed","hoses","host","hosts","hot","hotch","hotel","hotly","hound","hour","hours","house","hove","hovel","hover","how","howdy","howl","howls","hoy","hub","hubby","hubs","hue","hued","hues","huff","huffs","huffy","hug","huge","hugh","hugs","huh","hula","hulk","hulks","hull","hulls","hum","human","humid","humor","hump","humps","humpy","hums","humus","hun","hunch","hung","hunk","hunt","hunts","hurl","hurls","huron","hurry","hurt","hurts","hush","husk","husks","husky","hussy","hut","hutch","huts","hydra","hydro","hyena","hymen","hymn","hymns","hype","hyped","hyper","hypes","hypo","hyrax","i'd","i'll","i'm","i've","iamb","ian","iata","ibex","ibi","ibid","ibis","ibsen","ice","iced","ices","icily","icing","icon","icons","icy","ida","idaho","idea","ideal","ideas","idem","ides","idiom","idiot","idle","idled","idler","idles","idly","idol","idols","idyl","idyll","igloo","iii","ikon","iliad","ilk","ill","ills","ilo","image","imago","imam","imams","imbed","imbue","imf","imp","impel","imply","imps","ina","inane","inapt","inca","inch","incur","index","india","indus","inept","inert","infer","info","infra","ingot","ink","inked","inks","inky","inlay","inlet","inn","inner","inns","input","inset","inter","into","inure","ion","ionic","ions","iota","iowa","ira","iran","iraq","iraqi","irate","ire","irene","iris","irish","irk","irked","irks","iron","irons","irony","isaac","isbn","isis","islam","isle","isles","islet","isn't","issue","it'd","it'll","it's","italy","itch","itchy","item","items","its","ivory","ivy","jab","jabs","jack","jacks","jacob","jade","jaded","jades","jaffa","jag","jags","jail","jails","jake","jam","jamb","james","jamey","jams","jan","jane","janet","janus","japan","jape","jar","jars","jason","jaunt","java","jaw","jawed","jaws","jay","jays","jazz","jazzy","jean","jeans","jeep","jeeps","jeer","jeers","jeff","jelly","jenny","jerk","jerks","jerky","jerry","jesse","jest","jests","jesus","jet","jets","jetty","jew","jewel","jewry","jews","jib","jibe","jibed","jibes","jibs","jiffy","jig","jigs","jill","jilt","jilts","jim","jimmy","jinn","jinx","jive","jived","jives","joan","job","jobs","jock","joe","joey","jog","jogs","john","join","joins","joint","joist","joke","joked","joker","jokes","jolly","jolt","jolts","jones","jot","jots","joule","jours","joust","jove","jowl","jowls","jowly","joy","joyce","joys","judas","judea","judge","judo","judy","jug","jugs","juice","juicy","juju","julia","julie","july","julys","jumbo","jump","jumps","jumpy","june","junes","jung","junk","junks","junta","jura","juror","jury","just","jut","jute","juts","kabul","kafka","kale","kaman","kapok","kaput","karat","karen","karma","kate","kathy","katie","kay","kayak","kbyte","kebab","keel","keels","keen","keep","keeps","keg","kegs","keith","kelly","kelp","ken","kenya","kepi","kept","kerf","ketch","kevin","key","keyed","keys","khaki","khan","kick","kicks","kid","kids","kiev","kill","kills","kiln","kilns","kilo","kilos","kilt","kilts","kim","kin","kind","kinds","king","kings","kink","kinks","kinky","kiosk","kips","kirk","kiss","kit","kite","kited","kites","kith","kits","kitty","kiwi","kiwis","klieg","knack","knave","knead","knee","kneed","kneel","knees","knell","knelt","knew","knife","knit","knits","knob","knobs","knock","knoll","knot","knots","know","known","knows","knox","knurl","koala","kobe","kodak","koran","korea","kraal","kraft","kraut","krone","kudos","kyoto","lab","label","labor","labs","lace","laced","lacer","laces","lack","lacks","lad","lade","laden","ladle","lads","lady","lag","lager","lagos","lags","laid","lain","lair","lairs","laity","lake","lakes","lamb","lambs","lame","lamp","lamps","lance","land","lands","lane","lanes","lank","lanky","laos","lap","lapel","laps","lapse","larch","lard","lards","large","lark","larks","larry","larva","laser","lash","lass","lasso","last","lasts","latch","late","later","latex","lath","lathe","laths","latin","laud","lauds","laugh","laura","lava","law","lawn","lawns","laws","lax","laxly","lay","laye","layer","lays","layup","laze","lazed","lazes","lazy","lea","lead","leads","leaf","leafs","leafy","leak","leaks","leaky","lean","leans","leant","leap","leaps","leapt","learn","leas","lease","leash","least","leave","led","ledge","lee","leech","leeds","leek","leeks","leer","leers","leery","lees","left","lefty","leg","legal","leges","leggy","legs","leigh","lemon","lemur","len","lend","lends","lenin","lens","lent","lento","leo","leon","leper","less","lest","let","lets","letup","levee","level","lever","levi","levy","lewd","ley","lhasa","liar","liars","libel","libra","libya","lice","lick","licks","lid","lids","lie","lied","liege","lien","liens","lies","lieu","life","lifer","lift","lifts","light","like","liked","liken","likes","lilac","lilly","lilt","lilts","lily","lima","limb","limbo","limbs","lime","limed","limes","limey","limit","limo","limp","limps","limy","linda","line","lined","linen","liner","lines","lingo","link","links","lint","lion","lions","lip","lipid","lips","lira","lisa","lisp","lisps","list","lists","liszt","lit","liter","lithe","live","lived","liven","liver","lives","livid","liz","llama","load","loads","loaf","loafs","loam","loamy","loan","loans","loath","lob","lobby","lobe","lobes","lobs","local","loch","lochs","lock","locks","locum","locus","lode","lodge","loft","lofts","lofty","log","logic","logo","logos","logs","loin","loins","loire","loll","lolls","lone","loner","long","longs","look","looks","loom","looms","loon","loony","loop","loops","loopy","loose","loot","loots","lop","lope","loped","lopes","lops","loral","loran","lord","lords","lore","lorry","lose","loser","loses","loss","lost","lot","loth","lots","lotus","loud","louis","louse","lousy","lout","louts","love","loved","lover","loves","low","lower","lowly","loyal","lrun","ltd","lucas","lucid","luck","lucks","lucky","lucre","ludic","lug","lugs","luis","luke","lull","lulls","lulu","lumen","lump","lumps","lumpy","lunar","lunch","lung","lunge","lungs","lupin","lurch","lure","lured","lures","lurid","lurk","lurks","lush","lust","lusts","lusty","lute","lutes","luxe","luzon","lye","lying","lymph","lynch","lynn","lynx","lyon","lyons","lyre","lyric","mac","macao","macaw","mace","maced","maces","mach","macho","macro","mad","madam","made","madly","mae","mafia","magi","magic","magna","maid","maids","mail","mails","maim","maims","main","maine","mains","maize","major","make","maker","makes","malay","male","males","mali","mall","malls","malt","malta","malts","malty","malum","mama","mamas","mambo","mamma","mammy","man","mane","manes","mange","mango","mangy","mania","manic","manly","mann","manna","manor","mans","manse","manx","many","mao","maori","map","maple","maps","mar","march","marco","mare","mares","maria","marie","mario","mark","marks","marry","mars","marsh","mart","marts","marx","mary","maser","mash","mask","masks","mason","mass","massy","mast","masts","mat","match","mate","mated","mater","mates","maths","mats","matte","matzo","maui","maul","mauls","mauve","maw","maxi","maxim","may","maybe","mayor","mays","maze","mazes","mdv","mead","meal","meals","mealy","mean","means","meant","meat","meats","meaty","mecca","medal","media","medic","meek","meet","meets","mega","mel","melba","melon","melt","melts","memo","memos","men","mend","mends","mente","menu","menus","meow","meows","mercy","mere","merge","merit","merry","mers","mesh","mess","messy","met","metal","meted","meter","meth","meths","metro","mew","mewed","mews","mezzo","mho","miami","mica","micah","mice","micro","mid","midas","midge","midst","mien","miens","miff","miffs","mig","might","mike","mil","milan","mild","mile","miles","milk","milks","milky","mill","milli","mills","mils","milt","mime","mimed","mimer","mimes","mimi","mimic","mince","mind","minds","mine","mined","miner","mines","mini","minim","mink","minks","minor","mint","mints","minty","minus","minx","mirth","miser","miss","missy","mist","mists","misty","mite","miter","mites","mitt","mix","mixed","mixer","mixes","mixup","moan","moans","moat","moats","mob","mobil","mobs","mocha","mock","mocks","mod","modal","mode","model","modem","modes","modi","modus","mogul","moist","molar","mold","molds","moldy","mole","moles","moll","molls","molly","molt","molts","momma","mommy","monet","money","monk","monks","mono","month","moo","mooch","mood","moods","moody","mooed","moon","moons","moor","moors","moos","moose","moot","moots","mop","mope","moped","mopes","mops","moral","moray","more","mores","morn","moron","morse","mort","moses","moss","mossy","most","mote","motel","motes","motet","moth","moths","motif","motor","motto","mound","mount","mourn","mouse","mousy","mouth","move","moved","mover","moves","movie","mow","mowed","mower","mown","mows","mrs","much","muck","mucks","mucky","mucus","mud","muddy","muff","muffs","mufti","mug","muggy","mugs","mulch","mule","mules","mull","mulls","multi","mum","mummy","mumps","mums","munch","mural","murky","muse","mused","muses","mush","mushy","music","musk","musky","must","musts","musty","mute","muted","mutes","muzak","myna","mynah","myrrh","myth","myths","nab","nabob","nabs","nadir","naf","nag","nags","nail","nails","naive","naked","name","named","names","nancy","nanny","nap","napes","nappy","naps","nasal","nasty","natal","nato","natty","naval","nave","navel","naves","navy","nay","nazi","nazis","nco","neap","near","nears","neat","neck","necks","nee","need","needs","needy","negro","nehru","neigh","neil","neo","neon","nepal","nero","nerve","nervy","nest","nests","net","nets","never","nevil","new","newer","newly","news","newsy","newt","newts","next","nib","nibs","nice","nicer","niche","nick","nicks","niece","nifty","nigel","niger","nigh","night","nil","nile","nine","nines","ninny","ninth","nip","nippy","nips","nit","niter","nitro","nits","nixon","nne","nnw","noah","nobel","noble","nobly","nod","nodal","node","nodes","nods","noel","noes","noise","noisy","nomad","non","none","nook","nooks","noon","noose","nor","norm","norms","norse","north","nose","nosed","noses","nosey","nosy","not","notae","notch","note","noted","notes","noun","nouns","nova","novae","novel","now","nude","nudes","nudge","nul","null","numb","numbs","nun","nuns","nurse","nut","nuts","nutty","nylon","nymph","oaf","oafs","oak","oaks","oakum","oar","oars","oases","oasis","oat","oath","oaths","oats","obese","obey","obeys","oboe","oboes","occur","ocean","ochre","octal","octet","odd","odder","oddly","odds","ode","odes","odium","odor","oed","oems","off","offal","offer","oft","often","ogle","ogled","ogles","ogre","ogres","ohio","ohm","ohmic","ohms","oil","oiled","oiler","oils","oily","okapi","okay","okra","old","olden","older","oldie","olds","olive","omaha","oman","omega","omen","omens","omit","omits","once","one","ones","onion","only","onset","onto","onus","onyx","oops","ooze","oozed","oozes","oozy","opal","opec","open","opens","opera","opine","opium","opt","opted","optic","opts","opus","oral","orals","orate","orb","orbed","orbit","orbs","order","ore","ores","organ","orgy","orion","osaka","oscar","osier","oslo","other","otter","ouch","ought","ounce","our","ours","oust","ousts","out","outdo","outer","outre","outs","ouzel","ova","oval","ovals","ovary","ovate","oven","ovens","over","overs","overt","ovine","ovoid","ovum","owe","owed","owes","owing","owl","owlet","owls","owly","own","owned","owner","owns","oxen","oxide","oxlip","ozone","pace","paced","pacer","paces","pack","packs","pact","pacts","pad","paddy","padre","pads","pagan","page","paged","pages","paid","pail","pails","pain","pains","paint","pair","pairs","pal","pale","paled","paler","pales","pall","palls","palm","palms","palo","pals","palsy","pam","pan","panda","pane","panel","panes","pang","pangs","panic","pans","pansy","pant","pants","panty","pap","papa","papal","paper","papua","par","parch","pare","pared","paris","park","parka","parks","parry","parse","part","parts","party","parva","pass","past","pasta","paste","pasty","pat","patch","pate","paten","pater","path","paths","patio","patna","pats","patsy","patty","paul","paula","pause","pave","paved","paves","paw","pawed","pawn","pawns","paws","pax","pay","payee","payer","pays","pea","peace","peach","peak","peaks","peaky","peal","peals","pear","pearl","pears","peas","peat","peaty","pecan","peck","pecks","pedal","pee","peed","peek","peeks","peel","peels","peep","peeps","peer","peers","pees","peeve","peg","pegs","pelt","pelts","pen","penal","pence","pend","pends","penis","penn","penny","pens","pent","peony","pep","peppy","peps","pepsi","per","perch","peril","perk","perks","perky","perm","perms","pert","perth","peru","pesky","peso","pest","pests","pet","petal","pete","peter","petro","pets","petty","pew","pewee","pews","phase","phd","phial","phil","phone","phony","photo","phyla","piano","pica","pick","picks","pie","piece","pied","pier","piers","pies","piety","pig","piggy","pigmy","pigs","pike","piker","pikes","pilaf","pile","piled","piles","pill","pills","pilot","pimp","pimps","pin","pinch","pine","pined","pines","ping","pings","pink","pinks","pins","pint","pints","pinup","pious","pip","pipe","piped","piper","pipes","pips","pique","pisa","piss","piste","pit","pitch","pith","pithy","piton","pits","pitt","pitta","pity","pivot","pixel","pixie","pixy","pizza","place","plaid","plain","plait","plan","plane","plank","plans","plant","plate","plato","play","plays","plaza","plea","plead","pleas","pleat","plebs","plied","plies","pliny","plod","plods","plop","plops","plot","plots","ploy","ploys","pluck","plug","plugs","plum","plumb","plume","plump","plums","plumy","plunk","plus","plush","pluto","ply","poach","pod","podia","pods","poe","poem","poems","poet","poets","point","poise","poke","poked","poker","pokes","poky","polar","pole","poled","poles","polio","polka","poll","polls","polo","poly","polyp","pomp","pond","ponds","pony","pooch","pooh","pool","pools","poona","poop","poor","pop","pope","popes","poppy","pops","porch","pore","pored","pores","porgy","pork","porno","port","ports","pose","posed","poser","poses","posh","posit","posse","post","posts","posy","pot","pots","potty","pouch","pound","pour","pours","pout","pouts","pow","power","pps","pram","prams","prank","prat","prawn","pray","prays","pre","preen","prep","press","prey","preys","price","prick","pride","pried","pries","prig","prim","prime","print","prior","pris","prism","privy","prize","pro","probe","prod","prods","prof","prom","prone","prong","proof","prop","props","pros","prose","proud","prove","provo","prow","prowl","prows","proxy","prude","prune","pry","psalm","psion","psych","pub","pubs","puck","pucks","pudgy","puff","puffs","puffy","pug","puis","puke","puked","pukes","pull","pulls","pulp","pulps","pulpy","pulse","puma","pump","pumps","pun","punch","punic","punk","punks","puns","punt","punts","puny","pup","pupae","pupil","puppy","pups","pure","puree","purer","purge","purl","purls","purr","purrs","purse","pus","push","pushy","puss","pussy","put","puts","putt","putts","putty","pygmy","pylon","pyre","pyres","qatar","qdos","qed","qimi","qjump","qls","qmon","qpac","qptr","qram","qtyp","quack","quad","quads","quae","quaff","quail","quake","quaky","qualm","quark","quart","quash","quasi","quay","quays","queen","queer","quell","query","quest","queue","quick","quid","quiet","quiff","quill","quilt","quip","quips","quire","quirk","quit","quite","quito","quits","quiz","quoit","quota","quote","rabat","rabbi","rabid","race","raced","racer","races","rack","racks","racy","radar","radii","radio","radix","radon","raft","rafts","rag","rage","raged","rages","rags","raid","raids","rail","rails","rain","rains","rainy","raise","raja","rajah","rake","raked","rakes","rally","ralph","ram","ramp","ramps","rams","ran","ranch","rand","randy","rang","range","rangy","rank","ranks","rant","rants","rap","rape","raped","rapes","rapid","raps","rapt","rare","rarer","rash","rasp","rasps","rat","rate","rated","rates","ratio","rats","rave","raved","ravel","raven","raver","raves","raw","ray","rayed","rayon","rays","raze","razed","razes","razor","reach","react","read","reads","ready","real","realm","ream","reams","reap","reaps","rear","rears","rebel","rebus","rebut","recap","recur","red","redo","reds","reed","reeds","reedy","reef","reefs","reek","reeks","reel","reels","ref","refer","refit","regal","reign","rein","reins","relax","relay","relic","rely","remit","renal","rend","rends","renee","renew","rent","rents","rep","repay","repel","reply","rerun","resat","reset","resin","resit","rest","rests","retch","retry","reuse","rev","revel","revs","revue","rex","rhine","rhino","rhode","rhone","rhyme","rial","rib","ribs","rice","rices","rich","rick","ricky","rid","ride","rider","rides","ridge","rids","rife","rifer","rifle","rift","rifts","rig","riga","right","rigid","rigor","rigs","rile","rim","rims","rind","rinds","ring","rings","rink","rinks","rinse","rio","riot","riots","rip","ripe","ripen","rips","rise","risen","riser","rises","risk","risks","risky","rite","rites","ritz","rival","riven","river","rivet","riyal","roach","road","roads","roam","roams","roan","roar","roars","roast","rob","robe","robed","robes","robin","robot","robs","roc","rock","rocks","rocky","rod","rode","rodeo","rods","roe","roes","roger","rogue","rohr","role","roles","roll","rolls","rom","roman","rome","romeo","romp","romps","roof","roofs","rook","rooks","room","rooms","roomy","roost","root","roots","rope","roped","ropes","ropy","rose","roses","rosy","rot","rota","rote","rotor","rots","rouen","rouge","rough","round","rouse","rout","route","routs","rove","roved","rover","roves","row","rowan","rowdy","rowed","rower","rows","roy","royal","rub","rubs","ruby","ruck","rucks","ruddy","rude","ruder","rue","rued","rues","ruff","ruffs","rug","rugby","rugs","ruin","ruing","ruins","rule","ruled","ruler","rules","rum","rumba","rummy","rumor","rump","rumps","run","rune","runes","rung","rungs","runic","runny","runs","rupee","rural","ruse","ruses","rush","rusk","rusks","rust","rusts","rusty","rut","ruth","ruts","rutty","ryan","rye","saber","sable","sabot","sacci","sack","sacks","sad","sadly","safe","safer","safes","sag","saga","sagas","sage","sages","sago","sags","said","sail","sails","saint","sake","sakes","salad","sale","sales","salic","sally","salon","salt","salts","salty","salve","salvo","sam","samba","same","samoa","sand","sands","sandy","sane","saner","sang","sank","santa","sap","sappy","saps","sara","sarah","sari","saris","sash","sat","satan","sate","sated","sates","satin","sauce","saucy","saudi","saul","sauna","saute","save","saved","saver","saves","savor","savoy","savvy","saw","sawed","sawn","saws","saxon","say","says","scab","scabs","scald","scale","scalp","scaly","scam","scamp","scams","scan","scans","scant","scar","scare","scarf","scars","scary","scene","scent","scion","scire","scoff","scold","scone","scoop","scoot","scope","score","scorn","scot","scots","scott","scour","scout","scow","scowl","scows","scram","scrap","screw","scrip","scrub","scrum","scuba","scud","scuff","scull","scum","scup","scurf","scut","sea","seal","seals","seam","seams","seamy","sean","sear","sears","seas","seat","seato","seats","sec","sect","sects","sedan","seder","see","seed","seeds","seedy","seek","seeks","seem","seems","seen","seep","seeps","seer","seers","sees","seine","seize","self","sell","sells","semen","semi","send","sends","sense","sent","seoul","sepal","sepia","sere","serf","serfs","serge","serif","serum","serve","servo","set","seth","sets","setup","seven","sever","sew","sewed","sewer","sewn","sews","sex","sexed","sexes","sexy","shack","shade","shady","shaft","shag","shah","shake","shaky","shall","sham","shame","shams","shane","shank","shape","shard","share","shark","sharp","shave","shaw","shawl","she","she'd","sheaf","shear","shed","sheds","sheen","sheep","sheer","sheet","sheik","shelf","shell","shied","shier","shies","shift","shim","shin","shine","shins","shiny","ship","ships","shire","shirk","shirt","shoal","shock","shod","shoe","shoes","shone","shoo","shook","shoot","shop","shops","shore","shorn","short","shot","shots","shout","shove","show","shown","shows","showy","shred","shrew","shrub","shrug","shun","shuns","shunt","shut","shuts","shy","shyer","shyly","sibyl","sic","sick","side","sided","sides","sidle","siege","sieve","sift","sifts","sigh","sighs","sight","sigma","sign","signs","sikh","sikhs","silk","silks","silky","sill","sills","silly","silo","silos","silt","silts","silty","simon","sin","sinai","since","sine","sinew","sing","singe","singh","sings","sink","sinks","sins","sinus","sioux","sip","sips","sir","sire","sired","siren","sires","sirs","sisal","sissy","sit","site","sited","sites","sits","situ","six","sixes","sixth","sixty","size","sized","sizes","skate","skeet","skein","skew","skews","ski","skid","skids","skied","skier","skies","skiff","skill","skim","skimp","skims","skin","skins","skip","skips","skirt","skis","skit","skits","skive","skulk","skull","skunk","sky","slab","slabs","slack","slag","slags","slain","slake","slam","slams","slang","slant","slap","slaps","slash","slat","slate","slats","slav","slave","slay","slays","sled","sleds","sleek","sleep","sleet","slept","slew","slice","slick","slid","slide","slim","slime","slims","slimy","sling","slink","slip","slips","slit","slits","slob","sloe","slog","slogs","sloop","slop","slope","slops","slosh","slot","sloth","slots","slow","slows","slug","slugs","slum","slump","slums","slung","slunk","slur","slurp","slurs","slush","slut","sluts","sly","slyer","slyly","smack","small","smart","smash","smear","smell","smelt","smile","smirk","smite","smith","smock","smog","smoke","smoky","smote","smug","smut","snack","snag","snags","snail","snake","snaky","snap","snaps","snare","snark","snarl","sneak","sneer","snick","snide","sniff","snip","snipe","snips","snob","snobs","snood","snoop","snore","snort","snout","snow","snows","snowy","snub","snubs","snuff","snug","soak","soaks","soap","soaps","soapy","soar","soars","sob","sober","sobs","sock","socks","sod","soda","sods","sofa","sofas","sofia","soft","soggy","soil","soils","solar","sold","sole","soles","solet","solid","solo","solos","solve","some","somme","son","sonar","song","songs","sonic","sonny","sons","sony","soon","soot","sooty","sop","soppy","sops","sore","sorer","sores","sorry","sort","sorts","sot","soul","souls","sound","soup","soups","soupy","sour","sours","souse","south","sow","sowed","sower","sown","sows","soy","soya","spa","space","spade","spain","span","spank","spans","spar","spare","spark","spars","spas","spasm","spat","spate","spats","spawn","speak","spear","spec","speck","sped","speed","spell","spelt","spend","spent","sperm","spew","spews","spice","spicy","spied","spies","spike","spiky","spill","spilt","spin","spine","spins","spiny","spire","spit","spite","spits","splay","split","spoil","spoke","spoof","spook","spool","spoon","spoor","spore","sport","spot","spots","spout","sprat","spray","spree","sprig","spry","spud","spuds","spun","spunk","spur","spurn","spurs","spurt","spy","squab","squad","squat","squaw","squib","squid","sse","ssw","stab","stabs","stack","staff","stag","stage","stags","staid","stain","stair","stake","stale","stalk","stall","stamp","stan","stand","stank","star","stare","stark","stars","start","stary","stash","state","stave","stay","stays","std","stead","steak","steal","steam","steed","steel","steep","steer","stele","stem","stems","step","steps","stern","stet","steve","stew","stews","stick","sties","stiff","stile","still","stilt","sting","stink","stint","stir","stirs","stoat","stock","stoic","stoke","stole","stomp","stone","stony","stood","stool","stoop","stop","stops","store","stork","storm","story","stout","stove","stow","stows","strap","straw","stray","strew","strip","strop","strum","strut","stub","stubs","stuck","stud","studs","study","stuff","stump","stun","stung","stunk","stuns","stunt","sty","style","styli","styx","sua","suave","sub","subs","such","suck","sucks","sudan","suds","sue","sued","suede","sues","suet","suez","sugar","suing","suit","suite","suits","sulfa","sulk","sulks","sulky","sully","sum","sums","sun","sung","sunk","sunny","suns","sunup","sup","super","supra","sups","sure","surf","surge","surly","susan","sushi","suus","swab","swabs","swag","swain","swam","swamp","swan","swank","swans","swap","swaps","swarm","swat","swath","swats","sway","sways","swear","sweat","swede","sweep","sweet","swell","swelt","swept","swift","swig","swill","swim","swims","swine","swing","swipe","swirl","swish","swiss","swoon","swoop","swop","swops","sword","swore","sworn","swum","swung","sylph","sync","synod","syria","syrup","tab","tabby","table","taboo","tabs","tacit","tack","tacks","tacky","tact","tacts","taffy","tag","tags","taiga","tail","tails","taint","take","taken","taker","takes","talas","talc","tale","tales","talk","talks","talky","tall","tally","talon","tam","tame","tamed","tamer","tames","tamil","tamp","tampa","tamps","tan","tang","tango","tangs","tangy","tank","tanks","tans","tansy","taos","tap","tape","taped","taper","tapes","tapir","taps","tar","tardy","tarns","taro","tarry","tars","tart","tarts","task","tasks","taste","tasty","tat","tatty","taunt","taut","tawny","tax","taxed","taxes","taxi","taxis","tea","teach","teak","teal","teals","team","teams","tear","tears","teas","tease","teat","teats","ted","teddy","tee","teem","teems","teen","teens","teeny","tees","teet","teeth","tel","tele","telex","tell","tells","temp","tempo","temps","tempt","ten","tench","tend","tends","tenet","tenor","tens","tense","tent","tenth","tents","tepid","term","terms","tern","terns","terra","terry","terse","test","tests","testy","texan","texas","text","texts","thai","than","thane","thank","that","thaw","thaws","the","theca","theft","their","them","theme","then","there","these","theta","they","thick","thief","thigh","thin","thing","think","third","this","thong","thorn","those","three","threw","throb","throe","throw","thud","thuds","thug","thugs","thumb","thump","thus","thyme","tiara","tiber","tibet","tibia","tic","tick","ticks","tics","tidal","tide","tides","tidy","tie","tied","tier","tiers","ties","tiff","tiffs","tiger","tight","tilde","tile","tiled","tiler","tiles","till","tills","tilt","tilth","tilts","tim","time","timed","timer","times","timex","timid","timor","tin","tina","tine","tinge","tinny","tins","tint","tints","tiny","tip","tips","tipsy","tire","tired","tires","tit","titan","tithe","title","tits","titus","tnt","toad","toads","toady","toast","today","todd","toddy","toe","toed","toes","tofu","tog","toga","togo","togs","toil","toils","token","tokyo","told","toll","tolls","tom","tomb","tombs","tome","tomes","tommy","ton","tonal","tone","toned","toner","tones","tong","tonga","tongs","tonic","tons","tony","too","took","tool","tools","toot","tooth","toots","top","topaz","tope","topic","tops","topsy","torah","torch","tore","torn","torso","tort","torus","tory","tosco","toss","tot","total","tote","toted","totem","totes","touch","tough","tour","tours","tout","touts","tow","towed","towel","tower","town","towns","tows","toxic","toxin","toy","toyed","toys","trace","track","tract","tracy","trade","trail","train","trait","tram","tramp","trams","trap","traps","trash","trawl","tray","trays","tread","treat","tree","trees","trek","treks","trend","tress","triad","trial","tribe","trice","trick","tried","tries","trill","trim","trims","trio","trios","trip","tripe","trips","trite","trod","troll","troop","trot","troth","trots","trout","trove","troy","truce","truck","true","truer","trues","truly","trump","trunk","truss","trust","truth","try","tryst","tub","tuba","tubas","tubby","tube","tuber","tubes","tubs","tuck","tucks","tudor","tuft","tufts","tufty","tug","tugs","tulip","tulsa","tummy","tumor","tun","tuna","tunc","tune","tuned","tuner","tunes","tunic","tunis","tunny","tuns","turbo","turf","turfs","turin","turk","turks","turn","turns","turvy","tusk","tusks","tutee","tutor","tutu","twain","twang","tweak","tweed","tweet","twice","twig","twigs","twill","twin","twine","twins","twirl","twist","twit","twits","two","twos","tying","tyler","type","typed","types","typo","tyrol","tyros","tyson","tythe","tzar","tzars","udder","ufo","ugh","ugly","ukase","ulcer","ultra","umber","unapt","unarm","unary","unbar","unbid","uncap","uncle","uncut","under","undid","undo","undue","unfed","unfit","unify","union","unit","unite","units","unity","unix","unman","unpeg","unpin","unsay","unset","untie","until","unto","unwed","unzip","upon","upped","upper","ups","upset","urban","urdu","urge","urged","urges","uric","urine","urn","urns","usa","usage","use","used","user","users","uses","usher","using","ussr","usual","usurp","usury","utah","uteri","utter","uvula","uzbek","vaduz","vague","vain","vale","vales","valet","valid","valor","value","valve","vamp","vamps","van","vane","vaned","vanes","vans","vapid","vapor","vary","vase","vases","vast","vat","vats","vault","vaunt","veal","veer","veers","vegan","veil","veils","vein","veins","vela","velum","venal","vend","vends","venia","venom","vent","vents","venue","venus","verb","verba","verbs","verdi","verge","versa","verse","verve","very","vest","vests","vet","veto","vets","vex","vexed","vexes","via","vials","vibes","vic","vicar","vice","vices","vicki","video","vie","vied","vies","view","views","vigil","vigor","vile","viler","villa","vim","vine","vines","vinyl","viol","viola","viols","viper","viral","virgo","virus","vis","visa","visas","visit","visor","vista","vital","vitas","vitro","viva","vivid","vixen","viz","vocal","vodka","vogue","voice","void","voids","vole","voles","volt","volta","volts","volvo","vomit","vote","voted","voter","votes","vouch","vow","vowed","vowel","vows","vox","vulva","vying","wad","wade","waded","wader","wades","wads","wafer","waft","wafts","wag","wage","waged","wager","wages","wagon","wags","waif","wail","wails","wain","waist","wait","waits","waive","wake","waked","waken","wakes","wales","walk","walks","wall","walls","wally","waltz","wan","wand","wands","wane","waned","wanes","wanly","want","wants","war","ward","wards","ware","wares","warm","warms","warn","warns","warp","warps","wars","wart","warts","warty","wary","was","wash","washy","wasp","wasps","waste","watch","water","watt","watts","wave","waved","waver","waves","wavy","wax","waxed","waxen","waxes","waxy","way","ways","we'd","we'll","we're","we've","weak","weal","weals","wean","weans","wear","wears","weary","weave","web","webs","wed","wedge","weds","wee","weed","weeds","weedy","week","weeks","weeny","weep","weeps","weepy","weft","weigh","weir","weird","weirs","weld","welds","well","wells","welsh","welt","welts","wen","wench","wend","wends","went","wept","were","west","wet","wetly","wets","whack","whale","wham","wharf","what","wheat","wheel","whelk","when","where","whet","whets","whew","whey","which","whiff","whig","while","whim","whims","whine","whiny","whip","whips","whir","whirl","whisk","whist","whit","white","whiz","who","whoa","whole","whom","whoop","whore","whorl","whose","why","whys","wick","wicks","wide","widen","wider","widow","width","wield","wife","wig","wigs","wild","wilds","wile","wiles","will","wills","wilt","wilts","wily","wimp","wimps","win","wince","winch","wind","winds","windy","wine","wined","wines","wing","wings","wink","winks","wino","wins","winy","wipe","wiped","wiper","wipes","wire","wired","wires","wiry","wise","wised","wiser","wish","wishy","wisp","wisps","wispy","wit","witch","with","wits","witty","wives","wizen","wnw","woe","woes","wok","woke","woken","wolf","wolfs","woman","womb","wombs","women","won","won't","wont","woo","wood","woods","woody","wooed","wooer","woof","wool","woos","word","words","wordy","wore","work","works","world","worm","worms","wormy","worn","worry","worse","worst","worth","would","wound","wove","woven","wow","wowed","wrack","wraf","wrap","wraps","wrath","wreak","wreck","wren","wrens","wrest","wrier","wring","wrist","writ","write","writs","wrong","wrote","wroth","wrung","wry","wryly","wsw","xenon","xerox","xii","xiii","xiv","xix","xmas","xray","xrays","xvi","xvii","xviii","xxi","xxii","xxiii","xxiv","xxix","xxv","xxvi","xxvii","xxx","yacht","yahoo","yak","yaks","yale","yalta","yam","yams","yang","yank","yanks","yap","yaps","yard","yards","yarn","yarns","yaw","yawed","yawl","yawls","yawn","yawns","yaws","yeah","year","yearn","years","yeas","yeast","yell","yells","yelp","yelps","yemen","yen","yes","yet","yeti","yetis","yew","yews","yid","yield","yin","ymca","yodel","yoga","yogi","yoke","yoked","yokel","yokes","yolk","yolks","yolky","yom","yon","yore","york","you","you'd","young","your","yours","youth","yoyo","yoyos","yuan","yukon","yule","yummy","ywca","zaire","zany","zap","zaps","zeal","zebra","zebu","zees","zen","zero","zeros","zest","zesty","zeus","zinc","zion","zip","zippy","zips","zloty","zonal","zone","zoned","zones","zoo","zoom","zooms","zoos"]
      Rn = Math.random

      for(m=2;m--;) setTimeout(()=>{
        mirrorsIframe.src = 'mirrors'
      },250*(m+1))
      if(1)setInterval(()=>{
        mirrorsIframe.src = 'mirrors'
      },9500)
      openPracticeFrame = tgt => {
        practiceContainer.innerHTML = ''
        let practiceFrame = document.createElement('iframe')
        practiceFrame.className = 'practiceFrame'
        practiceFrame.src = tgt
        practiceContainer.appendChild(practiceFrame)
        returnButton.style.display = 'block'
      }

      genGameKey = () =>{
        tokens = []
        for(let i=3;i--;){
          do{
            newToken = shortWords[Rn()*shortWords.length|0]
          }while(tokens.filter(v=>v===newToken).length);
          tokens = [...tokens, newToken]
        }
        return tokens.join(' ')
      }
      
      br = () => document.createElement('br')
      
      hideModals = () => {
        modals = document.querySelectorAll('.modal').forEach(el=>{
          el.style.display = 'none'
        })
      }
      
      loadButton = (buttonData, fromModal=false) => {
        let button = document.createElement('button')
        button.className = 'button'
        let buttonContent = document.createElement('div')
        buttonContent.className = 'gameThumbs'
        buttonContent.id = buttonData.content.thumbId
        if(fromModal){
          button.style.transform = 'scale(2)'
        }else{
          buttonContent.onclick = e => {
            e.preventDefault()
            e.stopPropagation()
            popupButton(buttonData)
          }
        }
        button.appendChild(buttonContent)
        button.appendChild(br())
        captionContainer = document.createElement('div')
        captionContainer.className = 'captionContainer'
        captionContainer.style.background = '#804'
        captionContainer.title = "practice\n" + buttonData.captionsPractice[0]
        buttonData.captionsPractice.map((caption, idx) => {
          let el = document.createElement('div')
          el.className = 'captionPractice'
          el.innerHTML = caption
          captionContainer.appendChild(el)
          if(idx<buttonData.captionsPractice.length-1) captionContainer.appendChild(br())
        })
        captionContainer.onclick = e => {
          e.preventDefault()
          e.stopPropagation()
          openPracticeFrame(buttonData.practiceFrameTgt)
          hideModals()
        }
        button.appendChild(captionContainer)

        captionContainer = document.createElement('div')
        captionContainer.className = 'captionContainer'
        captionContainer.style.background = '#086'
        captionContainer.style.marginTop = '10px;'
        buttonData.captionsLive.map(caption=>{
          let el = document.createElement('div')
          el.title = 'launch a\nMULTIPLAYER ARENA\nfor ' + buttonData.captionsPractice[0]
          el.className = 'captionLive'
          el.innerHTML = caption
          captionContainer.appendChild(el)
        })
        captionContainer.onclick = e => {
          let link = document.createElement('a')
          link.target = '_blank'
          link.href = buttonData.liveLink
          link.style.position = 'fixed'
          link.style.visibility = 'hidden'
          document.body.appendChild(link)
          link.click()
          link.remove()
          hideModals()
        }
        button.appendChild(captionContainer)
        return button
      }

      popupButton = buttonData => {
        buttonModal.innerHTML = ''
        buttonModal.appendChild(loadButton(buttonData, true))
        buttonModal.style.display = 'flex'
      }

      loadButtons = () => {
        practiceContainer.innerHTML = ''
        buttonData = [
          {
            practiceFrameTgt: 'tictactoe_practice',
            content: {
              thumbId: 'tictactoeThumb',
            },
            captionsPractice:[
              'TIC TAC TOE',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=tictactoe'
          },
          {
            practiceFrameTgt: 'trektris_practice',
            content: {
              thumbId: 'trektrisThumb',
            },
            captionsPractice:[
              'TREKTRIS',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              '',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=trektris'
          },
          {
            practiceFrameTgt: 'orbs_practice',
            content: {
              thumbId: 'orbsThumb',
            },
            captionsPractice:[
              'ORBS',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=orbs'
          },
          {
            practiceFrameTgt: 'sidetoside_practice',
            content: {
              thumbId: 'sideToSideThumb',
            },
            captionsPractice:[
              'SIDE TO SIDE',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=sidetoside'
          },
          {
            practiceFrameTgt: 'puyopuyo_practice',
            content: {
              thumbId: 'puyopuyoThumb',
            },
            captionsPractice:[
              'PUYO PUYO',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=puyopuyo'
          },
          {
            practiceFrameTgt: 'battleracer_practice',
            content: {
              thumbId: 'battleracerThumb',
            },
            captionsPractice:[
              'BATTLERACER',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=battleracer'
          },
          {
            practiceFrameTgt: 'spelunk_practice',
            content: {
              thumbId: 'spelunkThumb',
            },
            captionsPractice:[
              'spelunk',
              '<span style="font-size: 1.5em;">PRACTICE</span>',
              ,'',
            ],
            captionsLive:[
              '<span style="font-size: 1.5em"> ARENA </span>'
            ],
            liveLink: '/launch/?gamesel=spelunk'
          },
        ]
        for(m=1;m--;)buttonData.map(buttonData => {
          practiceContainer.appendChild( loadButton(buttonData, false) )
        })
      }
      loadButtons()
      
      returnToGames = () => {
        returnButton.style.display = 'none'
        loadButtons()
      }
      
      console.log(genGameKey())
    </script>
  </body>
</html>
