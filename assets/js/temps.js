$(document).ready(function () {

    const rootUrl = 'http://localhost:8080/mysite/expense_t/'

    $(".top-modal-opener").on("click", function (event) {
        event.stopPropagation()
        $(".top-modal").toggleClass("active")
      })

      $(".treeview-toggle").on("click", function (e) {
        e.preventDefault()
        const $parent = $(this).closest(".treeview")
        $parent.toggleClass("open")

        $(".treeview").not($parent).removeClass("open")
      })

      $(".left-menu-toggle").on("click", function (event) {
        event.stopPropagation()
        $(".sidebar").toggleClass("active")
      })
      
    let table = $('.tables').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
      })


      //main ajax


    function ajax(url, method, data, target=false) {
        $.ajax({
            url: url,
            type: method,
            data: data,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".loader-wrapper").addClass("active")
            },
            success: function (response) {
                $(".loader-wrapper").removeClass("active")
                $('.response-wrapper').addClass("active")

                let res = {}
                try {
                    res = typeof response === "string" ? JSON.parse(response) : response
                } catch {
                    res = {status: "error", message: "Invalid server response"}
                }

                if (res.status === 'success') {
                    if ($('.response-wrapper').hasClass("error")) {
                        $('.response-wrapper').removeClass("error")
                    }
                    $('.response-wrapper').addClass("success")
        
                } else {
                    $('.response-wrapper').addClass("error")
                }

                $('.response-wrapper .response-text p').text(res.message)


                // if(target === 'reject'){
                //     console.log(target)
                // }

                
            },
            error: function (error, xhr, status) {
                console.error(error)
                $(".loader-wrapper").removeClass("active")
                $('.response-wrapper').addClass("active")
                $('.response-wrapper').addClass("error")
                $('.response-wrapper .response-text p').text('An error occured. Please try again')
            }
        })
    }

        $(".error").hide()
         // Removing error message on input
         $("input, select, textarea").on("input change", function () {
             const $field = $(this)
             const $error = $field.siblings(".error")
 
             if ($field.val().trim() !== "" && $field.val() !== "Select A Service" && $field.val() !== "Select An Option") {
                 $error.hide()
             }
         })

         function validateForm($form) {
                let isValid = true

                $form.find("input, textarea, select").each(function () {
                    const $field = $(this)
                    const $error = $field.siblings(".error")

                    if (!$field.val() || $field.val().trim() === "") {
                        $error.text("This field is required").show()
                        isValid = false
                    } else {
                        $error.hide()
                    }
                })

                return isValid
        }

        $(document).on("submit", function (e) {
            e.stopPropagation()
        })


        if ($(".forms").length > 0 && $("#editor-container1").length > 0) {
            let descriptionQuill = new Quill('#editor-container1', {
                theme: 'snow'
            })
            const form = document.querySelector(".forms")

            let description = document.getElementById("item_desc")


            form.addEventListener("submit", function () {
                description.value = descriptionQuill.root.innerHTML
            })
        }

        


        //ADDING ITEMS (CATEGORIES, EXPENSES)

        $(document).on("submit", '.addition-forms', function (e) {
            e.preventDefault()
            const form = new FormData(this)
            validateForm($(this))

            ajax(`${rootUrl}public/api/addit`, 'POST', form)
        })





        $(document).on("click", '.delete-item-btn', function (e) {
            e.preventDefault()

            $form = $(this).parent('form')

            if (confirm("Are you sure you want to delete this data. This action cannot be undone.")) {
                const form = $(this).parent('form')[0]
                const formData = new FormData(form)
                ajax('../public/api/action-power', 'POST', formData, $form)

                $(this).closest(".item-row").fadeOut(300, function () {
                    $(this).remove()
                })
            }
         })

         $(document).on("click", function (e) {
            if ($(".response-wrapper") && !$(".response-wrapper").has(e.target).length && !$(".response-wrapper").is(e.target)) {
                $(".response-wrapper").removeClass("active");
            }
        })

        const res = $('#server-response');
        if (res) {
            res.addClass('active');
            setTimeout(() => res.removeClass('active'), 5000);
        }



        //exporting

        $(document).on("click", '.export-btns', function () {
            const type = $(this).attr("data-type")
            const date = $(this).attr("data-date")
            window.location.href = `http://localhost:8080/mysite/TeamLore/exp-attendance?t=${type}&d=${date}`
        })

        
        //invoicing

//         document.getElementById('download-invoice').addEventListener('click', function() {
//     const invNo = this.dataset.inv;
//     if(!invNo) return;

    
//     const url = `${webUrl}inv/inv_download.php?inv=${encodeURIComponent(invNo)}`;

//     window.location.href = url;
// });

        $(document).on("click", '.download-invoice', function () {
            const docNo = $(this).data("inv")
            const doc = $(this).data("type")

           
            if (!docNo) return
            
            const url = `${rootUrl}inv/inv_download?type=${doc}&ref=${encodeURIComponent(docNo)}`
            window.location.href = url
        })


})