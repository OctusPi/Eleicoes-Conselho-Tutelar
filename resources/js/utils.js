import tables from "./tables"
import alerts from "./alerts"
import IMask from "imask"

class utils {
	viewload(visibility = false) {
		const indicator = document.querySelector('.load')
		if (indicator) {
			visibility ? indicator.classList.remove('d-none') : indicator.classList.add('d-none')
		}
	}

	sessiontime(minutes) {
		const viewtime = document.getElementById('sessiontime')
		if (viewtime) {
			let seconds = 59
			setInterval(function () {
				seconds--
				if (seconds == 0) {
					seconds = 59
					minutes--
				}
				viewtime.innerHTML =
					minutes >= 0 ? minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0') : '00:00'
			}, 1000)
		}
	}

	chntheme() {
		const triger = document.getElementById('changetheme');
		if (triger) {
			triger.addEventListener('change', e => {
				const status = e.target.checked;
				if (status) {
					document.documentElement.classList.add('dark')
				} else {
					document.documentElement.classList.remove('dark')
				}

				const url = status
					? '?app=dashboard&action=theme&type=dark'
					: '?app=dashboard&action=theme'

				axios.get(url).then(res => {
					console.log(res.status)
				});
			})
		}
	}

	setcode(field) {
		if(field){
			const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
			const digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']

			const size = 8
			let code = ''

			for (let i = 0; i < size; i++) {
				let matriz = (i > 0 && i % 2 === 0) ? digits : letters
				let key = Math.floor(Math.random() * matriz.length)
				code += matriz[key]
			}

			const prefix = field.dataset.code;

			field.value = prefix ? prefix+'-'+code : code
		}
	}

	loaddata() {
		if(tables.container){
			const url = (window.location + '&action=data').replace('#', '')
			this.viewload(true)

			axios({
				method: "GET",
				url: url,
			}).then(res => {
				if (res.status == 200) {
					//show start table
					if(res.data.dataview.header){
						tables.table(res.data.dataview)
					}

					//show start story call
					if(res.data.dataview.historys){
						tables.historys(res.data.dataview)
					}
					
				} else {
					alerts.send({ type: "danger", info: "Falha ao Carregar Dados" })
				}
			}).catch(error => {
				alerts.send({ type: "danger", info: "Falha Sistemica no Servidor!" })
				console.log(error)
			}).finally(() => {
				this.viewload(false)
			})
		}
	}

	mask(){
		const pattners = {
            maskcpf  : { mask: '000.000.000-00' },
            maskcnpj : { mask: '00.000.000/0000-00' },
            maskphone: { mask: '(00) 0.0000-0000' },
            maskdata : { mask: '00/00/0000' },
            maskcep : { mask: '00000-000' },
            masknis : { mask: '000.00000.00-0' },
            maskinep : { mask: '0000-0000' },
            maskhora : { mask: '00:00' },

            masknumb : {
                mask: Number,
                min: 0,
                max: 100000000000,
                radix: ".",
            },

            maskmoney: {
                mask: Number,
                min: 0,
                max: 100000000000,
                thousandsSeparator: ".",
                scale: 2,
                padFractionalZeros: true,
                normalizeZeros: true,
                radix: ",",
                mapToRadix: ["."]
            }
        }

		for(let pattner in pattners){
			const elements = [...document.getElementsByClassName(pattner)]
			if(elements){
				elements.forEach(element => {
					IMask(element, pattners[pattner])
				})
			}
		}
	}
}

export default new utils()