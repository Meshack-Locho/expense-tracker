$(document).ready(function(){

    const homeUrl = 'http://localhost:8080/mysite/b2b_demo/'

    // Toggle Forms
    $('.tab-btn').click(function(){
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.form-section').removeClass('active');
        $('#' + $(this).data('target')).addClass('active');
    });

    // Mock Login Validation
    const mockUsers = [
        { email: "alpha@demo.com", password: "123456", id: 1 },
        { email: "beta@demo.com", password: "123456", id: 2 }
    ];

    const mockAdmin = [
        {
            email: 'mike.otieno@demo.com', password: '123456', id: 1
        },
        {
            email: 'james.drew@demo.com', password: "123456", id: 2
        }
    ]

    $('#loginBtn').click(function(){
        const email = $('#loginEmail').val().trim();
        const password = $('#loginPassword').val().trim();
        const loginFor = $(this).data("for")
        let array
        
    
        if (loginFor === 'cus') {
            array = mockUsers
        }else if (loginFor === 'admin'){
            array = mockAdmin
        }else{
            return
        }

        

        const user = array.find(u => u.email === email && u.password === password);

        if(user){
            $('#loginMes').text("Login Successful. Redirecting to dashboard...").fadeIn();
            if ($('#loginMes').hasClass("error")) {
                $('#loginMes').removeClass("error");
                $('#loginMes').addClass("success");
            }
            setTimeout(()=>{
                window.location.href = `${homeUrl}login_handler?user_id=${user.id}&login=${loginFor}`;
            }, 4000)
        } else {
            $('#loginMes').text("Account not found. Please request access.").fadeIn();
            $('#loginMes').addClass("error");
        }
    });

    // Mock Account Request Submission
    // $('#registerBtn').click(function(){
    //     $('#registerSuccess')
    //         .text("Your request has been submitted for review.")
    //         .fadeIn();
    // })


});