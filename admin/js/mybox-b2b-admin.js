const apiCall = function() {
	const isProduction = document.getElementById("is_production").checked;
	const prefix = isProduction ? "" : "staging.";
	const siteUrl =
		"https://" + prefix + "api.myboxlogistics.io/public/b2b-check-auth";
	const accessKey = document.getElementById(
		isProduction ? "api_key_0" : "api_key_staging"
	).value;

	if (!accessKey) {
		return;
	}

	document.getElementById("loading").style.display = "block";

	jQuery.ajax({
		type: "GET",
		beforeSend: function(request) {
			request.setRequestHeader("Access-Key", accessKey);
		},
		url: siteUrl,
		success: function(response) {
			if (response.status == false) {
				document.getElementById("message").style.display = "block";
				document.getElementById("loading").style.display = "none";
				return;
			}
			document.getElementById("the_button").click();
		},
		error: function(response) {
			document.getElementById("message").style.display = "block";
			document.getElementById("loading").style.display = "none";
		}
	});
};

const open_xa_modal = function(item) {
	const modal = document.getElementById("myModal");
	modal.style.display = "block";
	renderModalData(item);
};

const renderModalData = function(data) {
	const modalTitle = document.getElementById("modal-title");
	modalTitle.innerText = "XA" + data.xa;
	const modalContent = document.getElementById("modal-content");
	modalContent.innerHTML = "";

	Object.keys(data).map((key) => {
		if (key === "id") {
			return;
		}

		if (key === "xa") {
			return;
		}

		if (key === "last_name") {
			return;
		}

		const holder = document.createElement("div");
		holder.className = "modal-content-item";

		const title = document.createElement("h4");
		title.innerText = key.replace(/_/g, " ");
		const content = document.createElement("p");
		content.innerText = data[key];

		if (key === "first_name") {
			title.innerText = "Name";
			content.innerText = data["first_name"] + " " + data["last_name"];
		}

		if (key === "fragile") {
			content.innerText = data[key] ? "Yes" : "No";
		}

		holder.appendChild(title);
		holder.appendChild(content);
		modalContent.appendChild(holder);
	});
};

const closeModal = function() {
	const modal = document.getElementById("myModal");
	modal.style.display = "none";
};

let lastCopied = null;
const copyTracking = function(xaNr, isProd) {
	const prefix = isProd ? "" : "staging.";
	try {
		navigator.clipboard.writeText(`https://${prefix}tracking.myboxlogistics.io/XA${xaNr}`).then(() => {
			if (lastCopied) {
				document.getElementById("info_" + lastCopied).style.display = "none";
				document.getElementById("button_" + lastCopied).style.display = "block";
			}
				document.getElementById("info_" + xaNr).style.display = "block";
				document.getElementById("button_" + xaNr).style.display = "none";	
				lastCopied = xaNr;		
		});
	} catch (e) {
		console.error(e);
	}
};
