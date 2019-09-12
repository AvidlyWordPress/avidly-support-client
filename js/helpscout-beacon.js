!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});

window.Beacon( 'config', {
	text: avidlyHelpScout.translations.text,
	labels: {
		sendAMessage: avidlyHelpScout.translations.sendAMessage,
		howCanWeHelp: avidlyHelpScout.translations.howCanWeHelp,
		responseTime: avidlyHelpScout.translations.responseTime,
		uploadAnImage: avidlyHelpScout.translations.uploadAnImage,
		attachAFile: avidlyHelpScout.translations.attachAFile,
		continueEditing: avidlyHelpScout.translations.continueEditing,
		lastUpdated: avidlyHelpScout.translations.lastUpdated,
		you: avidlyHelpScout.translations.you,
		nameLabel: avidlyHelpScout.translations.nameLabel,
		subjectLabel: avidlyHelpScout.translations.subjectLabel,
		emailLabel: avidlyHelpScout.translations.emailLabel,
		messageLabel: avidlyHelpScout.translations.messageLabel,
		messageSubmitLabel: avidlyHelpScout.translations.messageSubmitLabel,
		next: avidlyHelpScout.translations.next,
		weAreOnIt: avidlyHelpScout.translations.weAreOnIt,
		messageConfirmationText: avidlyHelpScout.translations.messageConfirmationText,
	},
});
window.Beacon("identify", {
	email: avidlyHelpScout.userEmail,
	signature: avidlyHelpScout.signature,
});
window.Beacon('prefill', {
	name: avidlyHelpScout.userName,
});

window.Beacon("init", avidlyHelpScout.beaconId ? avidlyHelpScout.beaconId : "");
