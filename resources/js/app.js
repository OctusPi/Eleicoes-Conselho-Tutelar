// import './bootstrap' /* production */
import '~b/js/bootstrap.bundle.min.js' /* developer */
import './axios'
import utils from './utils'
import forms from './forms'

//utilitary methods
utils.loaddata()
utils.mask()
utils.viewimg()

// forms requests and reponses
forms.create()
forms.actbtns()
forms.loadselcts()

// click restore form to add register
const addbtn = document.getElementById('addbtn')
const regform = document.querySelector("#modalRegister form")
if (addbtn && regform) {
	addbtn.addEventListener('click', e => {
		forms.restore(regform)

		//generate a unique identify code if necessary
		const inpcode = document.getElementById('cod')
		utils.setcode(inpcode)
	})
}