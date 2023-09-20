class alerts {

	constructor(boxID = 'box-alert') {
		this.box = document.getElementById(boxID)
		this.type = {
			success: { style: 'text-bg-success', defmsg: 'Operação Realizada com Sucesso!<br>' },
			warning: { style: 'text-bg-warning', defmsg: 'Falha ao Executar Operação!<br>' },
			info: { style: 'text-bg-info', defmsg: 'Informações: ' },
			danger: { style: 'text-bg-danger', defmsg: 'Erro: ' }
		}
	}

	send(alert) {
		const info = alert.info ?? '';
		if (this.box) {
			this.box.innerHTML = this.box.innerHTML = this.type[alert.type].defmsg + info

			const toastAlert = document.getElementById('toast')
			const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastAlert)

			this.defstyle(toastAlert, this.type[alert.type].style)
			toastBootstrap.show()
		}
	}

	remove() {
		if (this.box) {
			this.box.innerHTML = ''
			const toastAlert = document.getElementById('toast')
			const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastAlert)
			toastBootstrap.hide()
		}
	}

	defstyle(toast, styletoast) {
		if (toast) {
			for (const chave in this.type) {
				if (this.type.hasOwnProperty(chave)) {
					toast.classList.remove(this.type[chave].style)
				}
			}
			toast.classList.add(styletoast)
		}
	}
}

export default new alerts()
