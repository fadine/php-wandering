
/*
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie=function(b,j,m){
    if(typeof j!="undefined"){
        m=m||{};
        
        if(j===null){
            j="";
            m.expires=-1
            }
            var e="";
        if(m.expires&&(typeof m.expires=="number"||m.expires.toUTCString)){
            var f;
            if(typeof m.expires=="number"){
                f=new Date();
                f.setTime(f.getTime()+(m.expires*24*60*60*1000))
                }else{
                f=m.expires
                }
                e="; expires="+f.toUTCString()
            }
            var l=m.path?"; path="+(m.path):"";
        var g=m.domain?"; domain="+(m.domain):"";
        var a=m.secure?"; secure":"";
        document.cookie=[b,"=",encodeURIComponent(j),e,l,g,a].join("")
        }else{
        var d=null;
        if(document.cookie&&document.cookie!=""){
            var k=document.cookie.split(";");
            for(var h=0;h<k.length;h++){
                var c=jQuery.trim(k[h]);
                if(c.substring(0,b.length+1)==(b+"=")){
                    d=decodeURIComponent(c.substring(b.length+1));
                    break
                }
            }
            }
        return d
}
};





/*
 JS WARS Mobile 1.0.0
 Copyright (C) 2010  Jonas Wagner
*/
if(typeof(console)=="undefined"){
    console={
        log:function(){}
    }
    }
function Canvas(b,c){
    var a=document.createElement("canvas");
    a.setAttribute("width",b);
    a.setAttribute("height",c);
    return a
    }
    function fillTextMultiline(b,e,a,f,d){
    if(!$.isArray(e)){
        e=e.split("\n")
        }
        for(var c=0;c<e.length;c++){
        b.fillText(e[c],a,f);
        f+=d
        }
    }
    function V2(d,c){
    this.a=d;
    this.b=c
    }
    v2=function(d,c){
    return new V2(d,c)
    };
    
V2.prototype.mul=function(a){
    return v2(this.a*a,this.b*a)
    };
    
V2.prototype.magnitude=function(){
    return Math.sqrt(this.a*this.a+this.b*this.b)
    };
    
V2.prototype.normalize=function(){
    return this.mul(1/this.magnitude())
    };
    
function TouchJoystick(b){
    var a=this;
    this.event=null;
    this.element=document.createElement("div");
    document.body.appendChild(this.element);
    $(this.element).css({
        width:"150px",
        height:"150px",
        position:"absolute",
        border:"2px solid white",
        "border-radius":"20px"
    }).css(b);
    this.innerElement=document.createElement("div");
    $(this.innerElement).css({
        width:"40px",
        height:"40px",
        position:"absolute",
        border:"4px solid white",
        "border-radius":"20px",
        background:"white"
    });
    this.element.appendChild(this.innerElement);
    this.element.ontouchstart=function(){
        return false
        };
        
    this.element.ontouchmove=function(c){
        a.event=c.targetTouches[0]
        };
        
    this.element.ontouchend=this.element.ontouchcancel=function(c){
        a.event=null;
        a.setPosition(75,75)
        };
        
    $(window).resize(function(){
        a.position=$(this.element).position()
        });
    this.position=$(this.element).position();
    this.setPosition(75,75)
    }
    TouchJoystick.prototype.tick=function(){
    if(this.event){
        this.setPosition(this.event.clientX-this.position.left,this.event.clientY-this.position.top)
        }
    };

TouchJoystick.prototype.setPosition=function(a,b){
    a=max(min(a,150),0);
    b=max(min(b,150),0);
    this.innerElement.style.left=a-20;
    this.innerElement.style.top=b-20;
    a-=75;
    b-=75;
    if(Math.abs(a)<5){
        a=0
        }
        if(Math.abs(b)<5){
        b=0
        }
        this.x=a/75;
    this.y=b/75
    };
    
function TouchButton(b){
    var a=this;
    this.element=document.createElement("div");
    $(this.element).css({
        width:"64px",
        height:"64px",
        position:"absolute",
    }).css(b);
    this.element.ontouchstart=function(){
        a.active=true;
        return false
        };
        
    this.element.ontouchend=this.element.ontouchcancel=function(c){
        a.active=false
        };
        
    document.body.appendChild(this.element);
    this.active=false
    }
    function max(d,c){
    return(d>c)?d:c
    }
    function min(d,c){
    return(d<c)?d:c
    }
    function rect_collision(d,c){
    return !(d.y+d.h<c.y||d.y>c.y+c.h||d.x+d.w<c.x||d.x>c.x+c.w)
    }
    function do_collosion(g,f){
    if(!$.isArray(g)){
        g=[g]
        }
        if(!$.isArray(f)){
        f=[f]
        }
        for(var k=0;k<g.length;k++){
        for(var h=0;h<f.length;h++){
            if(rect_collision(g[k],f[h])){
                var n=g[k];
                var m=f[h];
                var l=min(n.hp/m.dmg,m.hp/n.dmg);
                n.hp-=m.dmg*l;
                m.hp-=n.dmg*l
                }
            }
        }
    }
function noise(a){
    return((((a+21541352)^1546733486)*(a^28308090)*(a^82014727))%1000000)/1000000
    }
    function noise2(a,b){
    return noise((a*23)^(b*13))
    }
    function smoothnoise(b,d){
    var c=Math.floor(b);
    var a=(noise(c)*2+noise(c-1)+noise(c+1))/4;
    return a
    }
    function smoothnoise2d(b,e){
    var c=Math.floor(b);
    var d=Math.floor(e);
    var a=noise2(c-1,d-1);
    a+=noise2(c,d-1);
    a+=noise2(c+1,d-1);
    a+=noise2(c-1,d);
    a+=noise2(c,d)*2;
    a+=noise2(c+1,d);
    a+=noise2(c-1,d+1);
    a+=noise2(c,d+1);
    a+=noise2(c+1,d+1);
    return a/10
    }
    var keyname={
    32:"SPACE",
    13:"ENTER",
    9:"TAB",
    8:"BACKSPACE",
    16:"SHIFT",
    17:"CTRL",
    18:"ALT",
    20:"CAPS_LOCK",
    144:"NUM_LOCK",
    145:"SCROLL_LOCK",
    37:"LEFT",
    38:"UP",
    39:"RIGHT",
    40:"DOWN",
    33:"PAGE_UP",
    34:"PAGE_DOWN",
    36:"HOME",
    35:"END",
    45:"INSERT",
    46:"DELETE",
    27:"ESCAPE",
    19:"PAUSE",
    222:"'"
};

window.levels=[];
window.Timer=function(){
    var a=this;
    a.ontick=function(){};
    
    a.last=new Date().getTime()/1000;
    a.tick=function(){
        var b=new Date().getTime()/1000;
        a.ontick(b-a.last);
        a.last=b
        };
        
    a.set_rate=function(b){
        a.rate=b;
        if(a.interval){
            clearInterval(a.interval)
            }
            a.interval=setInterval(a.tick,1000/b)
        }
    };

window.ParallaxScroller=function(b,a){
    this.layers=b
    };
    
window.ParallaxScroller.prototype.draw=function(b,f){
    var a,c,e;
    for(var d=0;d<this.layers.length;d++){
        e=this.layers[d];
        c=e[2];
        a=Math.round(e[0]+e[1]*f%c.width);
        b.drawImage(c,a,0);
        b.drawImage(c,(a+c.width),0)
        }
    };
    
window.ResourceLoader=function(){
    this.total=0;
    this.loaded=0
    };
    
window.ResourceLoader.prototype.load=function(d,c,b){
    var a=this;
    var e=document.createElement(d);
    a[c]=e;
    a.total++;
    $(e).one("error",function(){
        console.log("error",b);
        a.loaded++
    });
    if(d=="video"||d=="audio"){
        $(e).one("canplaythrough",function(){
            a.loaded++
        });
        e.setAttribute("autobuffer","autobuffer");
        e.setAttribute("src",b);
        e.load()
        }else{
        e.setAttribute("src",b);
        $(e).one("load",function(){
            a.loaded++
        })
        }
    };

window.main=function(){
    if("standalone" in window.navigator&&!window.navigator.standalone){
        var a=new google.bookmarkbubble.Bubble();
        a.show_();
        return
    }
    if(!("ontouchstart" in document.documentElement)){
        alert("browser does not support touch events, redirecting to the desktop version");
        location.href="http://29a.ch/jswars/";
        return
    }
    if(document.body.clientWidth<document.body.clientHeight){
        $("#message").fadeIn("slow");
        window.setTimeout(main,100);
        return
    }
    $("#message").remove();
    window.html5av=false;
    window.no_video=navigator.productSub=="20090423";
    window.data=new ResourceLoader();
    data.load("img","player_ship","gfx/player_ship.png");
    data.player_ship.frames=10;
    data.load("img","rocket","gfx/rocket%20animated.png");
    data.rocket.frames=1;
    data.load("img","nuke","gfx/nuke.png");
    data.nuke.frames=10;
    data.load("img","asteroid","gfx/asteroid.png");
    data.load("img","mine","gfx/mine.png");
    data.load("img","background0","gfx/background0.jpg");
    data.load("img","background1","gfx/background1.png");
    data.load("img","background2","gfx/background2.png");
    data.load("img","shot","gfx/shot.png");
    data.load("img","eye","gfx/eye.png");
    data.load("img","eye_explosion","gfx/eye_explosion.png");
    data.eye_explosion.frames=4;
    data.load("img","mine_blink","gfx/mine_blink.png");
    data.mine_blink.frames=2;
    data.load("img","explosion0","gfx/explosion.png");
    data.explosion0.frames=17;
    data.load("img","explosion1","gfx/explosion2.png");
    data.explosion1.frames=17;
    data.load("img","explosion2","gfx/explosion3.png");
    data.explosion2.frames=17;
    data.explosion=[data.explosion1,data.explosion2,data.explosion0];
    data.load("img","missile_powerup","gfx/missile_powerup.png");
    data.missile_powerup.frames=19;
    data.load("img","radioactive_powerup","gfx/radioactive_powerup.png");
    data.radioactive_powerup.frames=19;
    data.load("img","skull","gfx/skull.png");
    data.skull.frames=30;
    data.load("img","fireball","gfx/fireball.png");
    if(html5av){
        data.load("audio","explosion_sound","data/explosion.ogg");
        data.load("audio","music","data/countdown-behind.ogg");
        data.music.volume=0;
        data.music.play()
        }
        window.display=$("#screen");
    display[0].width=min(document.body.clientWidth,1024);
    display[0].height=min(document.body.clientHeight,768);
    display[0].style.width=document.body.clientWidth;
    display[0].style.height=document.body.clientHeight;
    window.width=display[0].width;
    window.height=display[0].height;
    window.ctx=display[0].getContext("2d");
    display[0].ontouchstart=function(){
        return false
        };
        
    window.timer=new Timer();
    window.intro=new Intro();
    window.highscores=JSON.parse($.cookie("window.highscores")||"[]");
    window.button1=new TouchButton({
        "background-image":'url("gfx/laser_button.png")',
        right:"10px",
        top:"10px"
    });
    window.button2=new TouchButton({
        "background-image":'url("gfx/missile_button.png")',
        right:"10px",
        top:"94px"
    });
    window.joystick=new TouchJoystick({
        top:"10px",
        left:"10px"
    });
    $(joystick.element).hide();
    $(button1.element).hide();
    $(button2.element).hide()
    };
    
window.Timeline=function(a){
    this.data=a||[];
    this.dirty=true
    };
    
window.Timeline.prototype.tick=function(a){
    if(this.dirty){
        this.data.sort(function(d,c){
            return d[0]-c[0]
            });
        this.data.reverse();
        this.dirty=false
        }while(this.data.length&&this.data[this.data.length-1][0]<=a){
        this.data.pop()[1]()
        }
    };

window.Timeline.prototype.add=function(a,b){
    this.push([a,b]);
    this.dirty=true
    };
    
window.Credits=function(){
    var c=20;
    timer.set_rate(30);
    var d=["Software Development: Jonas Wagner","3D Artwork: Jonas Wagner","2D Artwork: Jonas Wagner","Sound Effects: Jonas Wagner","Music: Countdown - Behind <http://www.countdown.fr.fm>"];
    var b=0;
    timer.ontick=function(i){
        b+=i;
        ctx.drawImage(data.background0,0,0);
        ctx.fillStyle="gold";
        var g=height-min(b,10)*20;
        var f=0;
        var e;
        ctx.font=c+"px sans-serif";
        fillTextMultiline(ctx,d,50,g,c*1.5);
        ctx.fillStyle="gold";
        ctx.font="20px sans-serif";
        ctx.fillText("Credits",(width-ctx.measureText("Credits").width)/2,40)
        };
        
    var a=$(document).one("keydown.credits",function(e){
        window.menu.enter()
        })
    };
    
window.Intro=function(){
    var k=this;
    var a=100;
    var h=3;
    var g=100;
    var j=32;
    var b=[];
    var d=0;
    function f(){
        return[(Math.random()-0.5)*width*Math.sqrt(a)/2,(Math.random()-0.5)*height*Math.sqrt(a)/2,a+d]
        }
        for(var c=0;c<g;c++){
        b.push([(Math.random()-0.5)*width*Math.sqrt(a)/2,(Math.random()-0.5)*height*Math.sqrt(a)/2,Math.random()*a+d])
        }
        function e(){
        if(data.total==data.loaded){
            ctx.restore();
            window.game=new Game(levels[0])
            }
            ctx.fillStyle="rgba(0,0,0,0.5)";
        ctx.fillRect(0,0,width,height);
        ctx.fillStyle="white";
        for(var n=0;n<b.length;n++){
            if(b[n][2]<=d){
                b[n]=f()
                }
                var q=Math.sqrt(b[n][2]-d);
            var l=(b[n][0]/q)+width/2;
            var r=(b[n][1]/q)+height/2;
            if(l<0||l>width||r<0||r>height){
                b[n]=f();
                continue
            }
            ctx.beginPath();
            ctx.arc(l,r,h/q,0,Math.PI*2,true);
            ctx.closePath();
            ctx.fill()
            }
            ctx.fillStyle="gold";
        ctx.font=j+"px sans-serif";
        var m=ctx.measureText("JS-TYPE").width;
        ctx.fillText("JS WARS",0,0);
        ctx.fillText("JS WARS",(width-m)/2,(height-j)/2);
        ctx.font=j/2+"px sans-serif";
        if(data.total>data.loaded){
            var p="Loading... please be patient";
            var m=ctx.measureText(p).width;
            ctx.fillText(p,(width-m)/2,(height-j/2)/2+j)
            }
            var o=128;
        ctx.strokeStyle="red";
        ctx.strokeRect((width-o)/2,height/2+j,o,25);
        ctx.fillRect((width-o)/2,height/2+j,o/data.total*data.loaded,25)
        }
        timer.ontick=function(i){
        d+=i;
        e()
        };
        
    ctx.save();
    timer.set_rate(15)
    };
    
window.Menu=function(){
    var c=this;
    var e=18;
    var b=[["Start Game",function(){
        c.exit();
        window.game=new Game(levels[0])
        }],["Highscores",function(){
        c.exit();
        new Highscores()
        }],["Credits",function(){
        c.exit();
        new Credits()
        }]];
    var d=0;
    ctx.save();
    function a(){
        ctx.fillStyle="black";
        ctx.fillRect(0,0,width,height);
        ctx.font=e+"px sans-serif";
        ctx.drawImage(data.background_menu,0,0,width,height);
        for(var g=0;g<b.length;g++){
            ctx.fillStyle=(g==d)?"red":"gold";
            var h=b[g][0];
            var f=ctx.measureText(h).width;
            ctx.fillText(h,(width-f)/2,200+g*e*1.5)
            }
        }
        this.enter=function(){
    timer.ontick=a;
    timer.set_rate(10);
    $(document).bind("keydown.menu",function(f){
        if(f.keyCode==40){
            d++;
            d%=b.length
            }
            if(f.keyCode==38){
            d--;
            if(d<0){
                d=b.length-1
                }
            }
        if(f.keyCode==32||f.keyCode==13){
        b[d][1]()
        }
    })
};

this.enter();
this.exit=function(){
    $(document).unbind("keydown.menu")
    }
};

window.Highscores=function(a,e){
    var j=this;
    if(a&&highscores.length<10||a>highscores[highscores.length]){
        if(e){
            window.highscores.push([a,prompt("You won - enter your name")])
            }else{
            window.highscores.push([a,prompt("Gameover - enter your name")])
            }
            window.highscores.sort(function(l,k){
            return k[0]-l[0]
            });
        if(window.highscores.length>10){
            window.highscores.pop()
            }
            $.cookie("window.highscores",JSON.stringify(window.highscores),{
            expires:365*10
        })
        }
        $(joystick.element).hide();
    $(button1.element).hide();
    $(button2.element).hide();
    var i=18;
    var f=width;
    var c=height;
    var d=Canvas(f,c);
    var b=d.getContext("2d");
    b.fillRect(0,0,f,c);
    ctx.save();
    var h=0;
    function g(o){
        h+=o;
        b.fillStyle="rgba(100, 150, 250, 0.8);";
        for(var m=0;m<f/4;m++){
            var l=(smoothnoise(m/6,h*2)*2+smoothnoise2d(m/4,h*6)*2+smoothnoise2d(m,h*8))/5;
            b.fillRect(m*4,c/4+l*c/4,2,2)
            }
            b.drawImage(d,Math.round(-f/100+(Math.random())),2,f+f/50,c+5);
        ctx.drawImage(d,0,0,width,height);
        ctx.fillStyle="gold";
        ctx.font=i+"px sans-serif";
        var p="Highscores";
        if(a){
            p=(e?"You won":"Game over")+" - Score: "+a
            }
            var k=ctx.measureText(p).width;
        ctx.fillText(p,(width-k)/2,80);
        p="Tap to continue";
        k=ctx.measureText(p).width;
        ctx.fillText(p,(width-k)/2,105);
        ctx.font="12px sans-serif";
        var n=[];
        for(var m=0;m<highscores.length;m++){
            n.push(highscores[m][0]+" - "+highscores[m][1])
            }
            fillTextMultiline(ctx,n,100,120+i,12*1.5)
        }
        timer.ontick=g;
    timer.set_rate(30);
    $(display).one("touchend",function(k){
        window.game=new Game(levels[0])
        })
    };
    
window.Game=function(f){
    var c=this;
    $(joystick.element).fadeIn("slow");
    $(button1.element).fadeIn("slow");
    $(button2.element).fadeIn("slow");
    c.enemies=new SpriteGroup();
    c.friends=new SpriteGroup();
    c.neutral=new SpriteGroup();
    c.graphics=new SpriteGroup();
    c.player=new Player(c,data.player_ship);
    c.player.x=20;
    c.player.y=(height-c.player.h)/2;
    c.friends.push(c.player);
    c.background=new ParallaxScroller([],width);
    c.level=new f(c);
    c.t=0;
    c.score=0;
    c.paused=false;
    ctx.save();
    if(html5av&&data.music){
        data.music.volume=1;
        data.music.play();
        try{
            data.music.currentTime=0
            }catch(g){
            console.log("could not set current time on music")
            }
        }
    function d(h,e){
    return !(h.y+h.h<e.y||h.y>e.y+e.h||h.x+h.w<e.x||h.x>e.x+e.w)
    }
    function a(){
    c.background.draw(ctx,c.t);
    c.player.draw(ctx);
    c.enemies.draw(ctx);
    c.graphics.draw(ctx);
    c.friends.draw(ctx);
    c.neutral.draw(ctx);
    ctx.fillStyle="gold";
    ctx.font="16px sans-serif";
    ctx.fillText("Score: "+c.score,180,25);
    ctx.fillText("Health: "+Math.round(c.player.hp),180,45);
    ctx.fillText(c.player.secondary_weapon.name+": "+c.player.secondary_weapon.ammo,180,65)
    }
    timer.ontick=function(m){
    if(c.paused){
        return
    }
    joystick.tick();
    c.t+=m;
    c.level.tick(m);
    var l=c.player;
    l.x+=l.speed*m*joystick.x;
    if(l.x<0){
        l.x=0
        }
        if(l.x+l.w>width){
        l.x=width-l.w
        }
        l.y+=l.speed*m*joystick.y;
    if(l.y<0){
        l.y=0
        }
        if(l.y+l.h>height){
        l.y=height-l.h
        }
        if(button1.active){
        l.primary_weapon.fire()
        }
        if(button2.active){
        l.secondary_weapon.fire()
        }
        var k,h;
    var e=c.enemies;
    c.player.tick(m);
    c.enemies.tick(m);
    c.friends.tick(m);
    c.graphics.tick(m);
    c.neutral.tick(m);
    c.enemies.cleanup();
    c.friends.cleanup();
    c.graphics.cleanup();
    c.neutral.cleanup();
    do_collosion(c.enemies.sprites,c.friends.sprites);
    do_collosion(c.player,c.enemies.sprites);
    do_collosion(c.player,c.neutral.sprites);
    do_collosion(c.neutral.sprites,c.friends.sprites);
    do_collosion(c.neutral.sprites,c.enemies.sprites);
    a();
    if(l.hp<=0||l.dead){
        if(html5av){
            data.music.pause()
            }
            new Highscores(c.score);
        $(document).unbind("keydown.game")
        }
    };

var b=$(document).bind("keydown.game",function(e){
    if(e.keyCode==27||e.keyCode==19){
        c.paused=!c.paused;
        if(c.paused){
            ctx.fillStyle="rgba(0, 0, 0, 0.5)";
            ctx.fillRect(0,0,width,height);
            ctx.fillStyle="gold";
            ctx.strokeStyle="black";
            ctx.font="32px sans-serif";
            var h=ctx.measureText("Paused").width;
            ctx.fillText("Paused",(width-h)/2,(height-32)/2);
            ctx.strokeText("Paused",(width-h)/2,(height-32)/2)
            }
        }
});
timer.set_rate(30)
};

window.Sprite=function(a){
    this.img=a;
    this.x=0;
    this.y=0;
    this.w=a.width;
    this.h=a.height;
    this.dead=false;
    this.hp=10;
    this.dmg=10
    };
    
Sprite.prototype.draw=function(a){
    a.drawImage(this.img,Math.floor(this.x),Math.floor(this.y),this.img.width,this.img.height)
    };
    
Sprite.prototype.tick=function(a){};
    
Sprite.prototype.die=function(){
    this.dead=true
    };
    
SpriteGroup=function(){
    this.sprites=[]
    };
    
SpriteGroup.prototype={};
    
SpriteGroup.prototype.push=function(a){
    this.sprites.push(a)
    };
    
SpriteGroup.prototype.tick=function(c){
    for(var b=0;b<this.sprites.length;b++){
        this.sprites[b].tick(c)
        }
        for(var b=0;b<this.sprites.length;b++){
        var a=this.sprites[b];
        if(a.hp<=0||a.x+a.w<0||a.x>width){
            a.die()
            }
        }
    };

SpriteGroup.prototype.draw=function(a){
    for(var b=0;b<this.sprites.length;b++){
        this.sprites[b].draw(a)
        }
    };
    
SpriteGroup.prototype.cleanup=function(){
    var b=this.sprites;
    this.sprites=[];
    for(var a=0;a<b.length;a++){
        if(!b[a].dead){
            this.sprites.push(b[a])
            }
        }
    };

SpriteGroup.prototype.splash_damage=function(b,a,g){
    for(var f=0;f<this.sprites.length;f++){
        var e=this.sprites[f];
        var d=a-e.x;
        var c=g-e.y;
        e.hp-=b/max(50,Math.sqrt(d*d+c*c))
        }
    };
    
SpriteGroup.prototype.area_damage=function(b,a,h,g){
    for(var f=0;f<this.sprites.length;f++){
        var e=this.sprites[f];
        var d=a-e.x;
        var c=h-e.y;
        if(Math.sqrt(d*d+c*c)<g){
            e.hp-=b
            }
        }
    };

window.Animation=function(a,b){
    Sprite.call(this,a);
    this.frames=a.frames;
    this.framerate=b;
    this.w=a.width/this.frames;
    this.t=0
    };
    
$.extend(Animation.prototype,Sprite.prototype);
Animation.prototype.tick=function(a){
    this.t+=a
    };
    
Animation.prototype.draw=function(c){
    var d=this.framerate;
    var b=this.img.width/this.frames;
    var a=Math.floor((this.t%(d*this.frames))/d)*b;
    c.drawImage(this.img,a,0,b,this.img.height,Math.floor(this.x),Math.floor(this.y),b,this.img.height)
    };
    
window.EyeExplosion=function(a,b){
    Animation.call(this,data.eye_explosion,0.1);
    this.x=a;
    this.y=b
    };
    
$.extend(EyeExplosion.prototype,Animation.prototype);
EyeExplosion.prototype.tick=function(a){
    this.t+=a;
    if(this.t>this.frames*this.framerate){
        this.die()
        }
    };

window.Explosion=function(a,b){
    Animation.call(this,data.explosion[Math.floor(Math.random()*data.explosion.length)],0.05);
    if(html5av){
        data.explosion_sound.cloneNode(true).play()
        }
        this.x=a-this.w/2;
    this.y=b-this.h/2;
    this.hp=10000;
    this.dmg=50
    };
    
$.extend(Explosion.prototype,Animation.prototype);
Explosion.prototype.tick=function(a){
    this.t+=a;
    if(this.t>this.frames*this.framerate){
        this.die()
        }
    };

window.Player=function(c,b){
    Animation.call(this,b,0.1);
    this.speed=200;
    this.hp=100;
    this.w-=25;
    var a=this;
    this.primary_weapon=new Weapon("Blaster",function(){
        return new Shot(a.x+a.w,a.y+a.h/3,400,60)
        },0.2,c.friends);
    this.secondary_weapon=new Weapon("Missiles",function(){
        return new Missile(a.x+a.w,a.y+a.h/3,250)
        },1,c.friends,5)
    };
    
$.extend(Player.prototype,Animation.prototype);
Player.prototype.draw=function(c){
    if(this.secondary_weapon.name=="Nuke"&&this.secondary_weapon.ammo>0){
        var b=data.nuke.width/data.nuke.frames;
        c.drawImage(data.nuke,0,0,b,data.nuke.height,Math.floor(this.x+10),Math.floor(this.y+16),b,data.nuke.height)
        }
        var d=this.framerate;
    var b=this.img.width/this.frames;
    var a=Math.floor((this.t%(d*this.frames))/d)*b;
    c.drawImage(this.img,a,0,b,this.img.height,Math.floor(this.x)-25,Math.floor(this.y),b,this.img.height)
    };
    
Player.prototype.tick=function(a){
    this.hp=min(this.hp+a,100);
    this.t+=a
    };
    
window.Weapon=function(b,a,c,e,d){
    this.name=b;
    this.projectile=a;
    this.rate=c;
    this.group=e;
    this.ammo=d||-1;
    this.t=0
    };
    
window.Weapon.prototype.fire=function(){
    if(game.t-this.t>this.rate&&this.ammo!=0){
        this.t=game.t;
        this.group.push(this.projectile());
        this.ammo--
    }
};

window.Projectile=function(a,d,c,b){
    Sprite.call(this,b);
    this.x=a;
    this.y=d;
    this.vx=c
    };
    
$.extend(Projectile.prototype,Sprite.prototype);
Projectile.prototype.tick=function(a){
    this.x+=this.vx*a;
    if(this.x>width||this.x+this.w<0){
        this.dead=true
        }
    };

window.Missile=function(a,d,c,b){
    Projectile.call(this,a,d,c,data.rocket);
    Animation.call(this,data.rocket,0.05);
    this.x=a;
    this.y=d;
    this.hp=1;
    this.dmg=100
    };
    
$.extend(Missile.prototype,Projectile.prototype,Animation.prototype);
Missile.prototype.tick=function(a){
    Projectile.prototype.tick.call(this,a);
    Animation.prototype.tick.call(this,a)
    };
    
Missile.prototype.die=function(){
    game.enemies.area_damage(100,this.x+this.w/2,this.y+this.h/2,100);
    game.graphics.push(new Explosion(this.x+this.w/2,this.y+this.h/2));
    this.dead=true
    };
    
window.Nuke=function(a,c,b){
    Projectile.call(this,a,c,b,data.nuke);
    Animation.call(this,data.nuke,0.05);
    this.x=a;
    this.y=c;
    this.hp=1;
    this.dmg=100
    };
    
$.extend(Nuke.prototype,Projectile.prototype,Animation.prototype);
Nuke.prototype.tick=function(a){
    Projectile.prototype.tick.call(this,a);
    Animation.prototype.tick.call(this,a)
    };
    
Nuke.prototype.die=function(){
    game.enemies.splash_damage(8000,this.x,this.y);
    new NukeEffect(this.x,this.y);
    this.dead=true
    };
    
window.Shot=function(b,e,d,a,c){
    Projectile.call(this,b,e,d,c||data.shot);
    this.dmg=a;
    this.hp=1
    };
    
Shot.prototype=Projectile.prototype;
window.RadioactivePowerup=function(a,d,c,b){
    Animation.call(this,data.radioactive_powerup,0.05);
    this.x=a;
    this.y=d;
    this.vx=c;
    this.vy=b;
    this.hp=10
    };
    
$.extend(RadioactivePowerup.prototype,Animation.prototype);
RadioactivePowerup.prototype.tick=function(a){
    Animation.prototype.tick.call(this,a);
    this.x+=this.vx*a;
    this.y+=this.vy*a
    };
    
RadioactivePowerup.prototype.die=function(){
    if(this.hp!=10){
        console.log("activated radioactive powerup");
        game.player.secondary_weapon=new Weapon("Nuke",function(){
            return new Nuke(game.player.x,game.player.y+16,100)
            },1,game.friends,1)
        }
        this.dead=true
    };
    
window.MissilePowerup=function(a,d,c,b){
    Animation.call(this,data.missile_powerup,0.05);
    this.x=a;
    this.y=d;
    this.vx=c;
    this.vy=b;
    this.hp=10
    };
    
$.extend(MissilePowerup.prototype,Animation.prototype);
MissilePowerup.prototype.tick=function(a){
    Animation.prototype.tick.call(this,a);
    this.x+=this.vx*a;
    this.y+=this.vy*a
    };
    
MissilePowerup.prototype.die=function(){
    if(this.hp!=10){
        console.log("activated missile powerup");
        game.player.secondary_weapon=new Weapon("Missiles",function(){
            return new Missile(game.player.x+game.player.w,game.player.y+game.player.h/3,300)
            },1,game.friends,5)
        }
        this.dead=true
    };
    
window.Mine=function(a,d,c,b){
    Sprite.call(this,data.mine);
    this.x=a;
    this.y=d;
    this.vx=c;
    this.vy=b;
    this.hp=20
    };
    
$.extend(Mine.prototype,Sprite.prototype);
Mine.prototype.tick=function(a){
    this.x+=this.vx*a;
    this.y+=this.vy*a;
    this.y+=Math.sin(game.t)*a*20
    };
    
Mine.prototype.die=function(){
    game.graphics.push(new Explosion(this.x+this.w/2,this.y+this.h/2));
    game.score+=10;
    this.dead=true;
    this.hp=25
    };
    
window.TrackingMine=function(a,c,b){
    Animation.call(this,data.mine_blink,1);
    this.x=a;
    this.y=c;
    this.v=b
    };
    
$.extend(TrackingMine.prototype,Animation.prototype);
TrackingMine.prototype.tick=function(b){
    if(this.x<game.player.x){
        this.t=1;
        var a=v2(this.x-game.player.x,this.y-game.player.y).normalize().mul(this.v*b);
        this.x+=a.a;
        this.y+=a.b
        }else{
        this.x+=this.v*b;
        this.t=0
        }
    };

TrackingMine.prototype.die=function(){
    game.graphics.push(new Explosion(this.x+this.w/2,this.y+this.h/2));
    game.enemies.area_damage(40,this.x+this.w/2,this.y+this.h/2,150);
    game.friends.area_damage(40,this.x+this.w/2,this.y+this.h/2,150);
    game.score+=15;
    this.dead=true
    };
    
window.SkullBoss=function(a,b){
    Animation.call(this,data.skull,0.05);
    this.x=a;
    this.y=b;
    this.hp=400;
    this.lastshot=0;
    this.v=0
    };
    
$.extend(SkullBoss.prototype,Animation.prototype);
SkullBoss.prototype.tick=function(a){
    this.t+=a;
    this.v=max(-1,min(1,(game.player.y-this.y-125)/200))*200;
    this.y+=this.v*a;
    if(this.t%1.5>1&&this.t-this.lastshot>0.5){
        this.lastshot=this.t;
        game.enemies.push(new Shot(this.x,this.y+125,-300,400,data.fireball))
        }
    };

SkullBoss.prototype.die=function(){
    new NukeEffect(this.x,this.y);
    game.score+=1000;
    this.dead=true
    };
    
window.Eye=function(a,d,c,b){
    Sprite.call(this,data.eye);
    this.x=a;
    this.y=d;
    this.vx=c;
    this.vy=b;
    this.hp=15
    };
    
$.extend(Eye.prototype,Sprite.prototype);
Eye.prototype.tick=function(a){
    this.x+=this.vx*a;
    this.y+=this.vy*a;
    if(Math.random()>0.99){
        game.enemies.push(new Shot(this.x,this.y+this.h/2,-400,400))
        }
    };

Eye.prototype.die=function(){
    game.graphics.push(new EyeExplosion(this.x,this.y));
    console.log("dead");
    game.score+=30;
    this.dead=true
    };
    
window.NukeEffect=function(c,b){
    var s=8;
    var g=width/s;
    var p=height/s;
    var m=new Canvas(g,p);
    var q=new Canvas(width,height);
    var e=m.getContext("2d");
    var a=q.getContext("2d");
    a.drawImage(display[0],0,0);
    e.drawImage(display[0],0,0,g,p);
    ctx.drawImage(m,0,0,width,height);
    var j=width*height*4;
    var f=ctx.getImageData(0,0,width,height);
    if(f==null){
        console.log("bdata is null, probably webkit");
        return
    }
    var k=0;
    var l=timer.ontick;
    var n=timer.rate;
    var d=100;
    var o=Math.PI*2/200;
    var i=-2;
    var r=e.getImageData(0,0,g,p);
    timer.ontick=function(w){
        var M=Math.sqrt,h=Math.sin,L=Math.round,t=r.data,z=f.data,C=Math.abs,E=Math.min,G=Math.max;
        k+=w;
        for(var B=0;B<g;B++){
            for(var A=0;A<p;A++){
                var H=(A*g+B)*4;
                var J=B*s;
                var I=A*s;
                var F=c-J;
                var u=b-I;
                var K=M(F*F+u*u);
                var D=h(K*o+k*i)*(d/(1+(K/100)*(K/100))/k);
                J=L(J+D*(F/K));
                I=L(I+D*(u/K));
                var v=(I*width+J)*4;
                if(v>j||v<1){
                    r[H+3]=0
                    }else{
                    t[H]=E(255,z[v]*2);
                    t[H+1]=E(255,z[v+1]*2);
                    t[H+2]=E(255,z[v+2]*2);
                    t[H+3]=E(255,15*C(D))
                    }
                }
            }
        e.putImageData(r,0,0);
    ctx.drawImage(q,0,0);
    ctx.drawImage(m,0,0,width,height);
    console.log("drawn");
    if(k>1.5){
    timer.ontick=l;
    timer.set_rate(n)
    }
}
};

levels.push(function(c){
    var b=this;
    var d=new SkullBoss(window.width-data.skull.width/data.skull.frames-10,window.height/2);
    function g(){
        var h=new ([Eye,TrackingMine][Math.round(Math.random()*1)])(window.width,Math.random()*(height-data.mine.height),-30-b.t*0.5,Math.random()*20-10);
        c.enemies.push(h)
        }
        function a(){
        if(Math.random()>0.5){
            c.enemies.push(new RadioactivePowerup(window.width,Math.random()*height,-250,0))
            }else{
            c.enemies.push(new MissilePowerup(window.width,Math.random()*height,-250,0))
            }
        }
    b.name="New Easy Level";
var f=[];
for(var e=1;e<1000;e++){
    f.push([e,g]);
    if(e%30==0){
        f.push([e,a])
        }
    }
f.push([150,function(){
    c.enemies.push(d)
    }]);
b.timeline=new Timeline(f);
    b.t=0;
    c.background.layers=[[0,0,data.background0]];
    if(width<800){
    c.background.layers.push([0,-5,data.background1])
    }
    c.background.layers.push([0,-20,data.background2]);
    b.tick=function(h){
    b.t+=h;
    c.player.dead|=d.dead;
    b.timeline.tick(b.t)
    }
});
$(function(){
    main()
    });