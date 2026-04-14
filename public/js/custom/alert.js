function alertErrorMessage(msg)
{
    Swal.fire({
      icon: "error",
      title: "Error...",
      text: msg,
      position: "top-end",
      toast: true,
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      customClass: {
            popup: "small-alert"
        }
    });
    return;
}

function alertSuccessMessage(msg)
{
    Swal.fire({
      icon: "success",
      title: "Success...",
      text: msg,
      position: "top-end",
      toast: true,
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      customClass: {
            popup: "small-alert"
        }
    });
    return;
}

function alertInfoMessage(msg)
{
    Swal.fire({
      icon: "info",
      title: "An Information",
      text: msg,
      position: "top-end",
      toast: true,
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      customClass: {
            popup: "small-alert"
        }
    });
    return;
}

function confirmDelete(url,texts=null) {
    if (!texts) {
        texts = "You won't be able to revert this action!";
    }
    Swal.fire({
        title: 'Please Confirm',
        text: texts,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        allowEscapeKey: false,
        //position: "top-end",
        //toast: true,
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the delete URL
            window.location.href = url;
        }
    });
}

function goToUrl(url) 
{
    window.location.href = url;
}

function confirmAndGotToUrl(title,msg,icon,url) {
    Swal.fire({
        title: title,
        text: msg,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        buttonsStyling: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        //position: "top-end",
        //toast: true,
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmAndGoToUrl(title,msg,icon,url) {
    Swal.fire({
        title: title,
        text: msg,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        buttonsStyling: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        //position: "top-end",
        //toast: true,
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmAndGotToUrlWithOutCancel(title,msg,icon,url) {
    Swal.fire({
        title: title,
        text: msg,
        icon: icon,
        showCancelButton: false,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        buttonsStyling: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        //position: "top-end",
        //toast: true,
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmReload(title,msg,icon) {
    Swal.fire({
        title: title,
        text: msg,
        icon: icon,
        showCancelButton: false,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        allowEscapeKey: false,
        //position: "top-end",
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}

function confirmLogout(url) 
{
    Swal.fire({
        text: "Are you sure want to Logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Logout!",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        allowEscapeKey: false,
        position: "top-end",
        toast: true,
        customClass: {
            popup: "small-alert"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the delete URL
            window.location.href = url;
        }
    });
}

