let foundNbhSorted = [];
let focusLossOutside = true;

const hasClass = function (element, className) {
	return (" " + element.className + " ").indexOf(" " + className + " ") > -1;
};

const inputLostFocus = function () {
	const zoneInput = document.getElementById("zone");
	const nbhModal = document.getElementById("zone-modal");
	if (hasClass(nbhModal, "d-block")) {
		zoneInput.value = "";
		document.getElementById("zone_id").value = "";
		clearzoneModal();
	}
};

const initializezone = function () {
	const parent = document.getElementById("zone_field");
	const modal = document.createElement("div");
	const zoneInput = document.getElementById("zone");
	modal.className = "zone-modal";
	modal.id = "zone-modal";
	parent.appendChild(modal);

	parent.addEventListener("click", (e) => {
		if (e.target.className === "zone-modal-p") {
			focusLossOutside = false;
			zoneInput.value = e.target.innerText;
			document.getElementById("zone_id").value = e.target.id;
			clearzoneModal();
		}
	});
	parent.addEventListener("focusin", (e) => {
		focusLossOutside = true;
	});

	parent.addEventListener("focusout", (e) => {
		window.setTimeout(() => {
			if (focusLossOutside) {
				inputLostFocus();
			}
		}, 150);
	});
	zoneInput.addEventListener("input", searchzone);
};

const searchzone = function (e) {
	const modal = document.getElementById("zone-modal");

	const search = e.target.value;
	if (search.replace(/ /g, "").length === 0) {
		clearzoneModal();
		return;
	}

	const foundNbh = [];

	zones.map((item) => {
		const zoneArr = item.name.toLowerCase().split(" ");
		const searchArr = search.toLowerCase().split(" ");
		let matched = 0;

		for (let i = 0; i < searchArr.length; i++) {
			const srch = searchArr[i];
			if (srch.replace(/ /g, "").length === 0) {
				break;
			}

			for (let j = 0; j < zoneArr.length; j++) {
				const nbh = zoneArr[j];
				if (nbh.indexOf(srch) > -1) {
					matched++;
				}
			}
		}

		if (!matched) {
			return;
		}

		foundNbh.push({
			matched,
			nbh: item.name,
			id: item.id,
		});
	});

	foundNbhSorted = foundNbh.sort(function (a, b) {
		const keyA = a.matched,
			keyB = b.matched;
		if (keyA > keyB) return -1;
		if (keyA < keyB) return 1;
		return 0;
	});

	if (foundNbhSorted.length > 0) {
		document.getElementById("zone-modal").innerHTML = "";
	} else if (foundNbh.length === 0) {
		modal.innerHTML = "";
		const p = document.createElement("p");
		p.innerText = "Zone not found!";
		p.className = "zone-modal-p-err";
		modal.appendChild(p);
	}

	foundNbhSorted.map((item) => {
		const p = document.createElement("p");
		p.innerText = item.nbh;
		p.id = item.id;
		p.className = "zone-modal-p";
		modal.appendChild(p);
	});
	modal.classList.add("d-block");
};

const clearzoneModal = function () {
	foundNbhSorted = [];
	document.getElementById("zone-modal").innerHTML = "";
	document.getElementById("zone-modal").classList.remove("d-block");
};

window.setTimeout(() => {
	initializezone();
}, 500);
