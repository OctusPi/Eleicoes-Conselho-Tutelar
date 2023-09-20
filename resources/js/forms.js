import asyncexc from "./asyncexc";
import alerts from "./alerts"

class forms {
	constructor() {
		this.slcfoms  = [...document.querySelectorAll("form")]
		this.slcloads = [...document.querySelectorAll(".loadtriger")]
	}

	check(form) {
		const errors = []
		if (form) {
			const inputs = [...form.querySelectorAll('input, textarea, select')]
			inputs.forEach(el => {
				if (el.dataset.required) {
					if (el.value === '') {
						el.classList.add('ocp-required')
						errors.push(el)
					} else {
						el.classList.remove('ocp-required')
					}
				}
			})
		}

		if (!(!errors.length)) {
			alerts.send({ type: "warning", info: "Campos Obrigatórios em Branco!" })
		} else {
			alerts.remove()
		}

		return errors
	}

	restore(form) {
		if (form) {
			form.reset()
			const idform = document.getElementById('id')
			if (idform) {
				idform.value = 0
			}
		}
	}

	hideform() {
		const modal    = document.getElementById('modalRegister')
		const backdrop = document.querySelector('.modal-backdrop')
		if (modal && backdrop) {

			// Fecha o modal
			modal.classList.remove('show')
			modal.style.display = 'none'
			backdrop.parentNode.removeChild(backdrop)

			// Restore body style
			document.body.classList.remove('modal-open')
			document.body.style = ""

			// Limpa o foco do teclado para evitar problemas de acessibilidade
			modal.setAttribute('aria-hidden', 'true')
			modal.removeAttribute('aria-modal')
			modal.removeAttribute('role')

			const myModal = new bootstrap.Modal(modal, {
				keyboard: false
			})

			myModal.hide()
		}
	}

	actbtns(){
		const parentAct = document.getElementById("data-view")
		if(parentAct){
			parentAct.addEventListener('click', e => {
				const edit     = e.target.matches("[data-edit]")
				const delet    = e.target.matches("[data-delete]")
				const download = e.target.matches("[data-href]")

				if(edit){
					const id   = e.target.dataset.edit
					const url  = (window.location+'&action=dataone&key='+id).replace('#', '')
					const form = document.querySelector("#modalRegister form")

					if(id && form){
						const params = {
							url : url,
							form: form
						}
						asyncexc.reqGet(params, asyncexc.resAsync)
					}
				}

				if(delet){
					const id   = e.target.dataset.delete
					const form = document.getElementById("form-delete")
					this.delete(form, id)
				}

				if(download){
					const url = e.target.dataset.href
					window.location = url
				}
			});
		}
	}

	loadselcts(){
		if(this.slcloads){
			this.slcloads.forEach(select => {
				select.addEventListener('change', e => {
					const element = e.target;
					const url = (window.location + '&action=options&key='+element.value).replace('#', '')
					const container = document.getElementById(element.dataset.load)
					const params = {
						url:url,
						container:container
					}
					asyncexc.reqGet(params, asyncexc.resAsync)
				})
			})
		}
	}

	create() {
		if (this.slcfoms) {
			const search = document.getElementById("form-search");
			(this.slcfoms).forEach(form => {
				form.addEventListener('submit', e => {
					const chk = this.check(e.target)
					if (e.target.dataset.request === 'async') {
						e.preventDefault()
						if (!chk.length) {
							const params = {
								form: e.target,
								search: search
							}
							asyncexc.reqPost(params, asyncexc.resAsync)
						}
					} else {
						if (chk.length) {
							e.preventDefault()
						}
					}
				})
			})
		}
	}

	update(form, json) {
		if (form && json) {
			this.restore(form)
			const fields = [...form.querySelectorAll('.ocp-input')]
			const checks = [...form.querySelectorAll('.ocp-check')]
			const values = json

			if (values) {
				//feed inputs, selects and multiples
				fields.forEach(field => {
					let type = field.type
					if (type !== 'file' && values[field.name]) {
						//select multiple fields
						if (Array.isArray(values[field.name])) {
							let arrfield = [...field];
							if (arrfield) {
								arrfield.forEach(element => {
									element.removeAttribute('selected')
									if (values[field.name].includes(element.value)) {
										element.setAttribute('selected', true)
									}
								})
							}
							//feed normal fields
						} else {
							field.value = values[field.name]
						}
					}
				})

				//feed checkbox and radios
				checks.forEach(check => {
					check.removeAttribute('checked')
					if (values[check.name]) {
						check.setAttribute('checked', true)
					}
				})

				//feed img container
				const frame = document.getElementById('ocpframeimage');
				if (frame) {
					if (values['foto']) {
						frame.innerHTML = `<img src="uploads/${values['foto']}" 
                        alt="" class="ocp-picture-imgform mx-auto"/>`
					}
				}
			}
		}
	}

	delete(form, id){
		if(form && id){
			const iddel = document.getElementById('iddelete')
			iddel.value = id;
		}
	}
}

export default new forms()
