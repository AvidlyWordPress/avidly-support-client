!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});

window.Beacon("init", avidlyHelpScout.beaconId ? avidlyHelpScout.beaconId : "");
window.Beacon( 'config', {
	text: avidlyHelpScout.text,
	labels: {
		sendAMessage: avidlyHelpScout.sendAMessage,
		howCanWeHelp: avidlyHelpScout.howCanWeHelp,
		responseTime: avidlyHelpScout.responseTime,
		uploadAnImage: avidlyHelpScout.uploadAnImage,
		attachAFile: avidlyHelpScout.attachAFile,
		continueEditing: avidlyHelpScout.continueEditing,
		lastUpdated: avidlyHelpScout.lastUpdated,
		you: avidlyHelpScout.you,
		nameLabel: avidlyHelpScout.nameLabel,
		subjectLabel: avidlyHelpScout.subjectLabel,
		emailLabel: avidlyHelpScout.emailLabel,
		messageLabel: avidlyHelpScout.messageLabel,
		messageSubmitLabel: avidlyHelpScout.messageSubmitLabel,
		next: avidlyHelpScout.next,
		weAreOnIt: avidlyHelpScout.weAreOnIt,
		messageConfirmationText: avidlyHelpScout.messageConfirmationText,
	},
	messaging: {
		contactForm: {
			"showName": true
		}
	}
});
window.Beacon("identify", {
	name: avidlyHelpScout.userName,
	email: avidlyHelpScout.userEmail,
});
