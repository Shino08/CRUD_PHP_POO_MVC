const formularios_ajax = document.querySelectorAll('.FormularioAjax');

formularios_ajax.forEach((formulario) => {

    formulario.addEventListener('submit', () => {
        e.preventDefault();

        Swal.fire({
            title: "¿Estas seguro de enviar el formulario?",
            text: "No podrás revertir esto!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, enviarlo!",
            cancelButtonText: "No, cancelar!"
        }).then((result) => {
            if (result.isConfirmed) {

                let data = new FormData(this);
                let method = this.getAttribute('method');
                let action = this.getAttribute('action');

                let encabezados = new Headers();

                let config = {
                    method: method,
                    headers: encabezados,
                    body: data,
                    cache: 'no-cache',
                    mode: 'cors'
                };

                fetch(action, config)
                    .then(response => response.json())
                    .then(response => {
                        return alertas_ajax(response);
                    })

            }
        });
    });
});

function alertas_ajax(alerta) {

    if (alerta.tipo == "simple") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Aceptar"
        });
    } else if (alerta.tipo == "recargar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    } else if (alerta.tipo == "limpiar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('.FormularioAjax').reset();
            }
        });

    }else if(alerta.tipo == "redireccionar"){
        window.location.href = alerta.url;
    }
}

// Boton de cerrar sesion

let btn_exit = document.getElementById('btn_exit');

btn_exit.addEventListener('click', (e) => {
  
    e.preventDefault();

            Swal.fire({
            title: "¿Estas seguro de cerrar sesion?",
            text: "No podrás revertir esto!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, cerrar sesion!",
            cancelButtonText: "No, cancelar!"
        }).then((result) => {
            if (result.isConfirmed) {

                let url = this.getAttribute('href');
                window.location.href = url;

            }
        });

}) 
