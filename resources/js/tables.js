class tables {
	constructor() {
		this.container = document.getElementById('data-view')
	}

	table(json) {
		if(this.container && json){
			
			if(json.header){
				// create table
				const table = document.createElement("table")
				table.classList.add("table","ocp-table")

				// create table header
				const theader = table.createTHead()
				const hline   = theader.insertRow()
				hline.classList.add("ocp-table-th")
				this.buildHeader(json, hline)

				// create table body
				const tbody = table.createTBody()
				this.buildBody(json, tbody)

				//remove last table
				while(this.container.firstChild){
					this.container.removeChild(this.container.firstChild)
				}
				this.tableInfo(json.body)
				this.container.appendChild(table)
			}else{
				//remove last table
				while(this.container.firstChild){
					this.container.removeChild(this.container.firstChild)
				}
				
				const msg = document.createElement("p")
				msg.classList.add("small", "text-center", "text-secondary")
				msg.textContent = "Aplique o filtro para localizar os registros"
				this.container.appendChild(msg)
			}
		}
	}

	tableInfo(json){
		const sizeJson = json.length
		const info  = document.createElement("div")
		const icon  = document.createElement("i")
		const total = document.createElement("span")

		info.classList.add("px-4", "pb-4", "text-end", "ocp-table-info")
		icon.classList.add("bi", "bi-grip-vertical")
		total.textContent = sizeJson +" "+ ((sizeJson > 1) ? " Registros Localizados" : " Registro Localizado")

		info.appendChild(icon)
		info.appendChild(total)
		this.container.appendChild(info)
	}

	buildHeader(json, hline) {
		if (json.header) {
			const header = json.header
			for (let value in header) {
				let th = document.createElement("th")
				th.textContent = header[value]
				hline.appendChild(th)
			}
		}
	}

	buildBody(json, tbody) {
		if (json.body) {
			const body = json.body
			body.forEach(line => {
				let row = tbody.insertRow()
				row.classList.add("ocp-table-tr")
				for(let index in json.header){
					let cell = row.insertCell()
					if(index === 'actions'){
						cell.classList.add("text-end", "text-truncate")
						this.buildActions(cell, line[index])
					}else{
						cell.textContent = line[index]
					}
				}
			});
			
		}
	}

	buildActions(cell, actions){
		if(actions){
			
			const configs = {
				"delete": {
					"class": ["btn-danger-hover", "bi-trash3"],
					"attrs": {"type":"button", "data-bs-toggle":"modal", "data-bs-target":"#modalDelete"}
				},
				"edit":{
					"class": ["btn-primary-hover", "bi-pencil"],
					"attrs": {"type":"button", "data-bs-toggle":"modal", "data-bs-target":"#modalRegister"}
				},
				"report":{
					"class": ["btn-primary-hover", "bi-layers-half"],
					"attrs": {"type": "button"}
				},
				"download":{
					"class": ["btn-primary-hover", "bi-arrow-down-circle"],
					"attrs": {"type": "button"}
				},
				"viewchamado":{
					"class": ["btn-primary-hover", "bi-eye"],
					"attrs": {"type": "button"}
				}
			}
			
			for(let act in actions){
				let btn = document.createElement("button")
				// set commom class to btn actions
				btn.classList.add("btn", "btn-sm", "btn-action-tab", "ms-2", "bi")
				// set especic class to any btn action
				btn.classList.add(...(configs[act].class))

				// set generic attrs frontend
				const attrs = configs[act].attrs
				for(let attr in attrs){
					btn.setAttribute(attr, attrs[attr])
				}

				// set unique attr get by backend
				const uniqueAttrs = actions[act]
				for(let uattr in uniqueAttrs){
					btn.setAttribute(uattr, uniqueAttrs[uattr])
				}

				cell.appendChild(btn)
			}
		}
	}

	historys(json){
		if(json.status && json.historys){
			const status   = json.status
			const historys = json.historys

			if(historys.length > 0){
				//update status view
				const contentStatus = document.getElementById('callStatus')
				if(contentStatus){
					contentStatus.textContent = status
				}

				//remove last bubles
				while(this.container.firstChild){
					this.container.removeChild(this.container.firstChild)
				}

				//show list updates
				historys.forEach(history => {
					const bublemsg = document.createElement("div")
					bublemsg.classList.add("buble-history")

					// top buble msg
					const divtop = document.createElement("div")
					divtop.classList.add('d-flex', 'mb-2')

					const divinfo = document.createElement("div")
					divinfo.classList.add("buble-history-info", "text-secondary", "small")
					const iconuser = document.createElement("i")
					iconuser.classList.add('bi', 'bi-person-bounding-box', 'me-2')
					const nameuser = document.createElement("span")
					nameuser.textContent = history.user
					divinfo.appendChild(iconuser)
					divinfo.appendChild(nameuser)
					

					const divstatus = document.createElement("div")
					divstatus.classList.add("buble-history-status", "ms-auto", "text-end", "small", this.statusColor(history.status))
					const icondata = document.createElement("i")
					icondata.classList.add('bi', 'bi-calendar-event', 'me-2')
					const datasts = document.createElement("span")
					datasts.textContent = (history.status ?? "")+" "+history.data
					divstatus.appendChild(icondata)
					divstatus.appendChild(datasts)

					divtop.appendChild(divinfo)
					divtop.appendChild(divstatus)

					// desc buble msg
					const divdesc = document.createElement("div")
					const pdesc = document.createElement("p")
					pdesc.classList.add("text", "small")
					pdesc.textContent = history.msg
					divdesc.appendChild(pdesc)


					bublemsg.appendChild(divtop)
					bublemsg.appendChild(divdesc)
					
					this.container.appendChild(bublemsg)
				})
			}else{
				//remove last bubles
				while(this.container.firstChild){
					this.container.removeChild(this.container.firstChild)
				}
				const msg = document.createElement("p")
				msg.classList.add("small", "text-center", "text-secondary")
				msg.textContent = "Nao existem atualizações de Status para exibir"
				this.container.appendChild(msg)
			}
		}
	}

	statusColor(status){
		const colors = {
			"Aberto":"text-danger",
			"Em Atendimento":"text-warning",
			"Solucionado":"text-primary",
			"Reaberto":"text-danger",
			"Finalizado":"text-success",
			null:"text-secondary"
		}

		return colors[status]
	}
}

export default new tables()