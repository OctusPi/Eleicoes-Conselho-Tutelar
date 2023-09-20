import alerts from "./alerts"
import utils from "./utils"
import tables from "./tables"
import forms from "./forms"

class asyncexc
{
    reqPost(params, response){
        alerts.remove()
        utils.viewload(true)

        const form   = params.form
        const search = params.search

        const dataform = new FormData(form)
        if (search && form.id !== search.id) {
            dataform.append('search', new URLSearchParams(new FormData(search)))
        }

        axios({
            method: form.method,
            url: form.action,
            data: dataform
        }).then(res => {
            if (res.status == 200) {
                params.res = res.data
                response(params)
            } else {
                alerts.send({ type: "danger", info: "Falha Sistemica no Servidor!" })
            }
        }).catch(error => {
            alerts.send({ type: "danger", info: "Falha Sistemica no Servidor!" })
            console.log(error)
        }).finally(() => {
            utils.viewload(false)
        })
    }

    reqGet(params , response){
        alerts.remove()
        utils.viewload(true)
        axios({
            method: "GET",
            url: params.url,
        }).then(res => {
            if (res.status == 200) {
                params.res = res.data
                response(params)
            } else {
                alerts.send({ type: "danger", info: "Falha Sistemica no Servidor!" })
            }
        }).catch(error => {
            alerts.send({ type: "danger", info: "Falha Sistemica no Servidor!" })
            console.log(error)
        }).finally(() => {
            utils.viewload(false)
        })
    }

    resAsync(params){
        const res = params.res
        if(res){
            
            //show message
            if (res.message) {
                alerts.send(res.message)
                if(res.message.type === 'success'){
                    forms.hideform()
                }
            }

            //feed selects async
            if(res.options && params.container){
                const options   = res.options
                const container = params.container

                while(container.firstChild){
					container.removeChild(container.firstChild)
				}
                
                const opt = document.createElement("option")
                opt.value = ""
                container.appendChild(opt)

                for(let index in options){
                    const opt = document.createElement("option")
                    opt.value = index
                    opt.textContent = options[index]
                    container.appendChild(opt)
                }
            }

            //feed form to edit
            if(res.dataobj){
                forms.update(params.form, res.dataobj)
            }
            
            //render data response to view
            if (res.dataview) {
                if(res.dataview.historys){
                    tables.historys(res.dataview)
                }else{
                    tables.table(res.dataview)
                }
            }

            //redirect page
            if (res.redirect) {
                window.location = res.redirect
                return
            }
        }
    }
}

export default new asyncexc()