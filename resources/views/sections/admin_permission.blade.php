@extends($layout)

@section('title', __('admin.title_permission'))

@section('breadcrumb')
<li><a href="#"><i class=""></i>Admin Permission</a></li>
@endsection
@section('css')
<style type="text/css">
    .admin.treeview li{
        list-style: none;
    }
    .admin.treeview label{
        width:80%;
    }
</style>
@endsection
@section('script')
@endsection
@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <!-- Default box -->
    <div class="box">
            <div class="box-header with-border">
            <h3 class="box-title">Set Admin Permission <span>({{$user->name}})</span></h3>
              </div>
            <div class="box-body">

                <form id="form" action='/admin/setpermission' method='post'>
                    @csrf
                    <div class="row">
                        <div class="col-md-12 form-group">
                        
                   
                            <div id="user_perm">

                            </div>
                                {{-- <ul class="admin treeview">
                                    @each('layouts.menulist',$menudata, 'menu')
                                </ul>  --}}
                        </div>             
                    </div>
                    <div class="form-group"">
                        <input type="submit" name="sub" class="btn btn-primary " value="Submit">
                    </div>
                    <input type="hidden" name="id" value="{{$id}}">
                </form>
            </div><!-- /.box-body -->
    </div><!-- /.box -->

</section>
@endsection

@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#ajax_loader_div').css('display','block');
    $.ajax({
      url: "/getadminpermission/{{$id}}",
      type: "GET",
      success: function(result) {
        $('#user_perm').append(result);

        $(document).find($('input[type="checkbox"]')).change(change_check_box);
        
        function checkChildCheckbox(ele)
        {
          var id = ele.attr('id');
          var status = ele.prop('checked');
          if(status == false)
          {
            ele.closest('table').find($('.'+id))
            .prop("checked" ,status)
            .prop("indeterminate" ,status)
            .removeClass('custom-checked custom-unchecked custom-indeterminate');
            ele
            .prop("indeterminate" ,status)
            .removeClass('custom-checked custom-unchecked custom-indeterminate');
          }
          else
          {
            ele.closest('table').find($('.'+id)).prop("checked" ,status)
            .removeClass('custom-checked custom-unchecked custom-indeterminate');
            prependClass(ele.closest('table').find($('.'+id)),'custom-checked');
            
            ele
            .removeClass('custom-checked custom-unchecked custom-indeterminate');
            prependClass(ele,'custom-checked');

          }
          ele.closest('table').find($('.'+id)).each(function(index,element){
            checkChildCheckbox($(element));
          });
          
        }
        function change_check_box(ele)
        {
          var ele = $(this);
          checkboxChanged1(ele);
          checkChildCheckbox(ele);
       }
        function prependClass(sel, strClass) {
          var $el = jQuery(sel);
          /* prepend class */
          var classes = $el.attr('class');
          classes = strClass +' ' +classes;
          $el.attr('class', classes);
        } 
        function getAll(eleClass,checked)
        {
          if(eleClass!='custom-checked' && eleClass!='custom-unchecked' && eleClass!='custom-indeterminate')
          {
            $(document).find("."+eleClass).each(function(){
              return all = ($(this).prop("checked") == checked);
            });
          }
          else return;
          return all;
        }
        function getCheckedProp(ele)
        {
          return ele.prop("checked");
        }
        function manageParent(eleclass,checked)
        {
          if(eleclass.length>0 && eleclass!=null && eleclass!= undefined)
          {
            eleclasses = eleclass.split(" ");
            for(i=0;i<eleclasses.length;i++)
            { 
              if(eleclasses[i]!='custom-checked' && eleclasses[i]!='custom-unchecked' && eleclasses[i]!='custom-indeterminate')
              {
                var all = getAll(eleclasses[i],checked);
                console.log('all',all);

                if(all && checked)
                {
                  $(document).find($("#"+eleclasses[i])).prop({
                      indeterminate: false,
                      checked: true
                  }).removeClass('custom-checked custom-unchecked custom-indeterminate');
                  prependClass($(document).find("#"+eleclasses[i]),checked ? 'custom-checked' : 'custom-unchecked');
                  parent_id = eleclasses[i];
                  //  if(parent_id!=null && parent_id!=undefined && parent_id!="")
                    // manageParent(parent_id,checked);
                  console.log('parent',parent_id,'i',i);
                }
              }
            }
          }
          return false;

        }
        function manageParent1(ele)
        {
          var eleclass = ele.attr("class");
          if(eleclass.length>0 && eleclass!=null && eleclass!= undefined)
          {
            eleclasses = eleclass.split(" ");
            for(i=0;i<eleclasses.length;i++)
            { 
              if(eleclasses[i]!='custom-checked' && eleclasses[i]!='custom-unchecked' && eleclasses[i]!='custom-indeterminate')
              {
                var all = getAll(eleclasses[i],checked);
                console.log('all',all);

                if(all && checked)
                {
                  $(document).find($("#"+eleclasses[i])).prop({
                      indeterminate: false,
                      checked: true
                  }).removeClass('custom-checked custom-unchecked custom-indeterminate');
                  prependClass($(document).find("#"+eleclasses[i]),checked ? 'custom-checked' : 'custom-unchecked');
                  parent_id = eleclasses[i];
                  console.log('parent',parent_id,'i',i);
                }
              }
            }
          }
          return false;

        }
        function checkboxChanged1(ele)
        {
            var checked = getCheckedProp(ele);
            if(!checked)
              ele.removeAttr("checked");
            else
              ele.attr("checked","checked");

              console.log('id - ',ele.attr("id"),"class - ",ele.attr("class"));
              
              var eleClass = ele.attr("class")
              manageParent(eleClass,checked);
              return;
            console.log('eleClass',eleClass);
            var all = getAll(eleClass,checked);
              console.log('all',all,'checked',checked)
              if(all && checked)
              {
                var indeterminate = getIndeterminateProp($(document).find("#"+eleClass),checked);
                $(document).find("#"+eleClass)
                .prop({
                    indeterminate: false,
                    checked: checked
                }).removeClass('custom-checked custom-unchecked custom-indeterminate');
                prependClass($(document).find("#"+eleClass),checked ? 'custom-checked' : 'custom-unchecked');
                
              var class111 = $(document).find("#"+eleClass).attr('class');
              var class11 =[];
              if(class111)
                class11 = class111.split(" ");
                console.log('class11',class11);
                for(i=0;i<class11.length;i++)
                {
                    $(document).find($("#"+class11[i]))
                    .prop({
                        indeterminate: false,
                        checked: checked
                    })
                    .removeClass('custom-checked custom-unchecked custom-indeterminate')
                    .addClass(checked ? 'custom-checked' : 'custom-unchecked');
                }
              }
              else if(all && !checked)
              {
                console.log('elecheck',$(document).find("."+eleClass+":checked"));
                indeterminate = $(document).find("."+eleClass+":checked").length > 0;
                $(document).find("#"+eleClass)
                .prop("checked", checked)
                .prop("indeterminate", indeterminate)
                .removeClass('custom-checked custom-unchecked custom-indeterminate');
                prependClass($(document).find("#"+eleClass),indeterminate ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));
                
                var class111 = $(document).find("#"+eleClass).attr('class');
                var class11 =[];
                if(class111)
                  class11 = class111.split(" ");
                for(i=0;i<class11.length;i++)
                {
                    indeterminate1 = $(document).find('.'+class11[i]+':checked').length > 0;      
                    indeterminate1 = $(document).find('.'+class11[i]+':indeterminate').length > 0 || indeterminate1;      
                    if(indeterminate1==false)
                    {                    
                      $(document).find($("#"+class11[i]))
                      .prop({
                          indeterminate: indeterminate1,
                          checked: checked
                      })
                      .removeClass('custom-checked custom-unchecked custom-indeterminate')
                      .addClass(indeterminate1 ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'))
                      .removeClass('custom-checked custom-unchecked custom-indeterminate')
                      .addClass(indeterminate1 ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));
                    }
                }
              }
              else if(!all && checked)
              {

              }
              else {
                $(document).find($("."+eleClass))
                .prop({
                    indeterminate: false,
                    checked: false
                })
                .removeClass('custom-checked custom-unchecked custom-indeterminate');
                prependClass($(document).find("#"+eleClass),'custom-indeterminate');
                var class111 = $(document).find($("#"+eleClass)).attr('class');
                var class11 =[];
                if(class111)
                  class11 = class111.split(" ");
                  console.log('class11', class11);
                for(i=0;i<class11.length;i++)
                {
                    $(document).find($("#"+class11[i]))
                    .prop({
                        indeterminate: true,
                        checked: true
                    }).removeClass('custom-checked custom-unchecked custom-indeterminate')
                    .addClass('custom-indeterminate')

                    .removeClass('custom-checked custom-unchecked custom-indeterminate')
                    .addClass('custom-indeterminate');
                  
                }
              }
              // if(eleClass!="" && eleClass!=null && eleClass!= undefined)
              //   checkboxChanged1(($(document).find("#"+eleClass) ) );
          }

        
        function checkboxChanged() {
          var $this = $(this),
              checked = $this.prop("checked"),
              container = $this.parent(),
              siblings = container.siblings();
          container.find('input[type="checkbox"]')
          .prop({
              indeterminate: false,
              checked: checked
          })
          .removeClass('custom-checked custom-unchecked custom-indeterminate')
          .addClass(checked ? 'custom-checked' : 'custom-unchecked');

          checkSiblings(container, checked);
          
        }
        function checkSiblings($el, checked) {
          var parent = $el.parent().parent(),
              all = true,
              indeterminate = false;

          $el.siblings().each(function() {
            return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
          });

          if (all && checked) {
            parent.children('input[type="checkbox"]')
            .prop({
                indeterminate: false,
                checked: checked
            })
            .removeClass('custom-checked custom-unchecked custom-indeterminate')
            .addClass(checked ? 'custom-checked' : 'custom-unchecked');

            checkSiblings(parent, checked);
          } 
          else if (all && !checked) {
            indeterminate = parent.find('input[type="checkbox"]:checked').length > 0;

            parent.children('input[type="checkbox"]')
            .prop("checked", checked)
            .prop("indeterminate", indeterminate)
            .removeClass('custom-checked custom-unchecked custom-indeterminate')
            .addClass(indeterminate ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));

            checkSiblings(parent, checked);
          } 
          else {
            $el.parents("li").children('input[type="checkbox"]')
            .prop({
                indeterminate: true,
                checked: true
            })
            .removeClass('custom-checked custom-unchecked custom-indeterminate')
            .addClass('custom-indeterminate');
          }
        }
        $('#ajax_loader_div').css('display','none');

      }
    });
  });

 
//     $(function() {
//       
//         function checkboxChanged() {
//           var $this = $(this),
//               checked = $this.prop("checked"),
//               container = $this.parent(),
//               siblings = container.siblings();
//           container.find('input[type="checkbox"]')
//           .prop({
//               indeterminate: false,
//               checked: checked
//           })
//           .siblings('label')
//           .removeClass('custom-checked custom-unchecked custom-indeterminate')
//           .addClass(checked ? 'custom-checked' : 'custom-unchecked');

//           checkSiblings(container, checked);
          
//         }
//         function checkSiblings($el, checked) {
//           var parent = $el.parent().parent(),
//               all = true,
//               indeterminate = false;

//           $el.siblings().each(function() {
//             return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
//           });

//           if (all && checked) {
//             parent.children('input[type="checkbox"]')
//             .prop({
//                 indeterminate: false,
//                 checked: checked
//             })
//             .siblings('label')
//             .removeClass('custom-checked custom-unchecked custom-indeterminate')
//             .addClass(checked ? 'custom-checked' : 'custom-unchecked');

//             checkSiblings(parent, checked);
//           } 
//           else if (all && !checked) {
//             indeterminate = parent.find('input[type="checkbox"]:checked').length > 0;

//             parent.children('input[type="checkbox"]')
//             .prop("checked", checked)
//             .prop("indeterminate", indeterminate)
//             .siblings('label')
//             .removeClass('custom-checked custom-unchecked custom-indeterminate')
//             .addClass(indeterminate ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));

//             checkSiblings(parent, checked);
//           } 
//           else {
//             $el.parents("li").children('input[type="checkbox"]')
//             .prop({
//                 indeterminate: true,
//                 checked: true
//             })
//             .siblings('label')
//             .removeClass('custom-checked custom-unchecked custom-indeterminate')
//             .addClass('custom-indeterminate');
//           }
//         }
// });
        function manageParent1(ele)
        {
          var eleclass = $(ele).attr("class");
          var checked = $(ele).prop("checked");
          if(eleclass.length>0 && eleclass!=null && eleclass!= undefined)
          {
            eleclasses = eleclass.split(" ");
            for(i=0;i<eleclasses.length;i++)
            { 
              if(eleclasses[i]!='custom-checked' && eleclasses[i]!='custom-unchecked' && eleclasses[i]!='custom-indeterminate')
              {
                var all = getAll(eleclasses[i],checked);
                console.log('all',all);

                if(all && checked)
                {
                $(document).find($("#"+eleclasses[i])).prop({
                      indeterminate: false,
                      checked: true
                  }).removeClass('custom-checked custom-unchecked custom-indeterminate').trigger('change');
                  prependClass($(document).find("#"+eleclasses[i]),checked ? 'custom-checked' : 'custom-unchecked');
                }
                
                else if(all && !checked)
                {
                  $(document).find($("#"+eleclasses[i])).prop({
                      indeterminate: false,
                      checked: false
                  }).removeClass('custom-checked custom-unchecked custom-indeterminate').trigger('change');
                  prependClass($(document).find("#"+eleclasses[i]),'custom-unchecked');
                

                }
                else 
                {
                  indeterminate = $(document).find("."+eleclasses[i]+":indeterminate").length>0;
                  indeterminate = $(document).find("."+eleclasses[i]+":checked").length>0 || indeterminate;
                  
                  $(document).find($("#"+eleclasses[i])).prop({
                      indeterminate: indeterminate,
                      checked: checked
                  }).removeClass('custom-checked custom-unchecked custom-indeterminate');
                  if(indeterminate && checked && all)
                    $(document).find($("#"+eleclasses[i]));
                  
                  prependClass($(document).find("#"+eleclasses[i]),indeterminate ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));
               
                }
              }
            }
          }
          return false;
        }
        function getAll(eleClass,checked)
        {
          if(eleClass!='custom-checked' && eleClass!='custom-unchecked' && eleClass!='custom-indeterminate')
          {
            $(document).find("."+eleClass).each(function(){
              return all = ($(this).prop("checked") == checked);
            });
          }
          else return;
          return all;
        }
        function prependClass(sel, strClass) {
          var $el = jQuery(sel);
          /* prepend class */
          var classes = $el.attr('class');
          classes = strClass +' ' +classes;
          $el.attr('class', classes);
        } 

</script>
@endsection