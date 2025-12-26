// reload browser
function realoadBrowser() {
    window.location.reload();
}

// alert confirm message
function confirmDeleteAlert(message) {
    return Swal.fire({
        title: '<span style="font-size: 22px"> Konfirmasi</span>',
        text: "Apakah anda yakin?",
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Tidak',
        confirmButtonText: 'Ya',
        reverseButtons: true,
        confirmButtonColor: '#48ABF7',
        cancelButtonColor: '#EFEFEF',
        customClass: {
            cancelButton: 'text-dark'
        }
    });
}



// alert success message
function successAlert(message) {
    return Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        showConfirmButton: false,
        timer: 1000,
    });
}

function errorAlert() {
    return Swal.fire({
        title: 'Error',
        text: 'Terjadi kesalahan!',
        icon: 'error',
        showConfirmButton: false,
        timer: 1000,
    });
}
function warningAlert(message) {
    Swal.fire({
        title: 'Peringatan !',
        text: message,
        icon: 'warning',
        timer: 5000,
        showConfirmButton: true,
        confirmButtonText: 'Ok',
        confirmButtonColor: '#FFAD46',
    });
}

function emailOrPasswordWrong() {
    return Swal.fire({
        title: 'Peringatan',
        text: 'username atau password anda salah !',
        icon: 'warning',
        timer: 5000,
        showConfirmButton: true
    });
}



function exportAlert(message) {
    return Swal.fire({
        title: '<span style="font-size: 22px"> Konfirmasi</span>',
        text: "Apakah anda yakin?",
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Tidak',
        confirmButtonText: 'Ya',
        reverseButtons: true,
        confirmButtonColor: '#48ABF7',
        cancelButtonColor: '#EFEFEF',
        customClass: {
            cancelButton: 'text-dark'
        }
    });
}
