/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
define("jquery-nos-link-wysiwyg",["jquery-nos","wijmo.wijtabs"],function(a){a.fn.extend({nosLinkWysiwyg:function(b){b=b||{expert:false,newlink:true,base_url:"",texts:{titleAppdeskPage:"Pick a new link",titleAppdeskMedia:"Pick a new media",titleProperties2:"Edit properties",titleProperties3:"Edit properties"}};return this.each(function(){var u=a(this).find("a[data-id=close]").click(function(E){E.preventDefault();u.nosDialog("close")}).end(),q=u.attr("id"),t,v,A=u.find(":radio[name=link_type]").click(function(){var E=a(this).val();if(E!==t){z.val("")}t=E;s(t,true)}),B=u.find("#"+q+"_properties").find("> form").nosFormValidate({submitHandler:function(){if(t==="external"&&!/^\w+\:/.test(z.val())){var I=b.base_url.match(/^(\w+\:\/\/)(.*)\/$/),G=I[1],J=I[2],H=z.val();if(H.indexOf(J)!=-1){z.val(G+H)}else{if(/^\w+\:\/\/[a-z0-9][a-z0-9-]*\.[a-z]{2,6}/.test(H)){z.val("http://"+H)}else{}}}var F=f.val();if(F.charAt(0)!=="?"){F="?"+F}var E={href:z.val()+(t==="internal"?F:""),title:d.val()};if(a.inArray("target",p[t])!==-1){E.target=B.find(":radio[name=target]:checked").val()}D.trigger("insert.link",E)}}).end(),z=u.find("#"+q+"_url"),f=u.find("#"+q+"_url_params"),d=u.find("#"+q+"_tooltip"),c=u.find("#"+q+"_anchor").change(function(){var E=a(this).find("option:selected").val();z.val(E?E:"")}),x=u.find("#"+q+"_email").change(function(){var E=a(this).val();z.val(E?"mailto:"+E:"")}),j=u.find("#"+q+"_phone").change(function(){var E=a(this).val();z.val(E?"tel:"+E:"")}),n=u.find("#"+q+"_url_real"),o=u.find("#"+q+"_title"),k=u.find("> ul").css({width:"18%"}),h=k.find("li:last a"),w=function(E){o.text(E.title);n.text(E.path);z.val("nos://media/"+E._id);u.wijtabs("enableTab",2).wijtabs("select",2)},C=function(E){o.text(E.page_title);n.text(E.url);z.val("nos://page/"+E._id);u.wijtabs("enableTab",2).wijtabs("select",2)},D=u.closest(".ui-dialog-content").bind("appdesk_pick_Nos\\Media\\Model_Media",function(F,E){w(E)}).nosListenEvent({name:"Nos\\Media\\Model_Media",action:"insert"},function(E){if(t==="media"){a.ajax({method:"GET",url:b.base_url+"admin/noviusos_media/appdesk/info/"+E.id,dataType:"json",success:function(F){w(F)}})}}).bind("appdesk_pick_Nos\\Page\\Model_Page",function(F,E){C(E)}).nosListenEvent({name:"Nos\\Page\\Model_Page",action:"insert"},function(E){if(t==="internal"){a.ajax({method:"GET",url:b.base_url+"admin/noviusos_page/appdesk/info/"+E.id,dataType:"json",success:function(F){C(F)}})}}),s=function(F,E){switch(F){case"internal":case"media":var G=F==="internal"?b.texts.titleAppdeskPage:b.texts.titleAppdeskMedia;if(k.find("li").size()===2){u.wijtabs("add","#"+q+"_appdesk",G,1);h.text(b.texts.titleProperties3)}else{k.find("li:eq(1) a").text(G)}break;case"external":case"anchor":case"email":case"phone":if(k.find("li").size()>2){u.wijtabs("remove",1);v=null;h.text(b.texts.titleProperties2)}u.wijtabs("enableTab",1);break}if(E){u.wijtabs("select",1)}},i=["title","url","anchor","email","phone","url_params","tooltip","target"],p={internal:["title","url","url_params","tooltip","target"],external:["url","tooltip","target"],media:["title","url","tooltip","target"],anchor:["anchor","tooltip"],email:["email","tooltip"],phone:["phone","tooltip"]},g=D.data("tinymce"),r=g.dom.getParent(g.selection.getNode(),"A"),m=g.dom.select("a.mceItemAnchor,img.mceItemAnchor");a.each(m,function(G,F){var E=g.dom.getAttrib(F,"name");if(E){a("<option></option>").val("#"+E).text(E).appendTo(c)}});a.each(i,function(E,F){B.find("#tr_"+q+"_"+F).hide()});if(r){var e=a(r),y=e.attr("href"),l;z.val(y);d.val(e.attr("title"));B.find(":radio[name=target]").eq(e.attr("target")?0:1).prop("checked",true);if(y.substr(0,11)==="nos://page/"){l=y.match(/nos:\/\/page\/(\d+)(.*)/i);if(l){t="internal";a.ajax({method:"GET",url:b.base_url+"admin/noviusos_page/appdesk/info/"+l[1],dataType:"json",success:function(E){o.text(E.page_title);n.text(E.url)}});z.val("nos://page/"+l[1]);f.val(l[2])}}else{if(y.substr(0,12)==="nos://media/"){l=y.match(/nos:\/\/media\/(\d+)/i);if(l){t="media";a.ajax({method:"GET",url:b.base_url+"admin/noviusos_media/appdesk/info/"+l[1],dataType:"json",success:function(E){o.text(E.title);n.text(E.path)}})}}else{if(y.substr(0,1)==="#"){t="anchor";c.find('option[value="'+y+'"]').prop("selected",true)}else{if(y.substr(0,7)==="mailto:"){t="email";x.val(y.replace("mailto:",""))}else{if(y.substr(0,4)==="tel:"){t="phone";j.val(y.replace("tel:",""))}else{t="external"}}}}}A.filter("[value="+t+"]").prop("checked",true)}u.wijtabs({alignment:"left",load:function(G,F){var E=a(F.panel).outerHeight(true)-a(F.panel).innerHeight();a(F.panel).height(D.height()-E)},disabledIndexes:b.newlink?[1]:[],select:function(H,G){var I=a(G.panel);if(I.attr("id")===(q+"_appdesk")){I.addClass("box-sizing-border");if(v!=t){I.empty().show().css({width:"100%",padding:0,margin:0}).load(t==="internal"?"admin/noviusos_page/appdesk/index/appdesk_pick":"admin/noviusos_media/appdesk/index/appdesk_pick");v=t}}else{if(G.panel===B[0]){var F=p[t],E=B.find(":radio[name=target]:checked");c.add(x).add(j).removeClass("required");u.find("#"+q+"_"+F[0]).addClass("required");a.each(i,function(J,K){B.find("#tr_"+q+"_"+K)[a.inArray(K,F)===-1?"hide":"show"]()});if(!b.expert&&t!=="external"){B.find("#tr_"+q+"_url").hide();B.find("#tr_"+q+"_url_params").hide()}if(!E.size()){B.find(":radio[name=target]").eq(t==="internal"?1:0).prop("checked",true)}if(a.inArray(t,["media","internal"])!==-1){n.show();z.hide()}else{n.hide();z.show()}}}},show:function(F,E){a(E.panel).nosOnShow()}}).find(".wijmo-wijtabs-content").css({width:"81%",position:"relative"}).addClass("box-sizing-border").end().nosFormUI();if(r){s(t);u.wijtabs("select",k.find("li").size()-1)}})}});return a});