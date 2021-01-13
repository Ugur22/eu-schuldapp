import AsyncStorage from '@react-native-async-storage/async-storage';

export async function getDocToken(token,dataIsReady,user) {
	// let that = scope;
	let value = '';
	try {
	value = await AsyncStorage.getItem('login');
	user = JSON.parse(value); 
	} catch (error) {
	// Error retrieving data
	console.log(error.message);
	}

	try {
	let response = await fetch(`http://api.arsus.nl/token`, {
		method: 'POST',
		headers: {
		'Content-Type': 'application/json',
		'Authorization': `Bearer ${user.token}`
		}
	});

	let responseJson = await response.json();
	if (responseJson.success) {
		dataIsReady = true;
		token = responseJson.token;

		return token;
		console.log(dataIsReady);
	} else {
		console.log(responseJson);
	}
	} catch (error) {
	console.log(error);
	console.error(error);
	}
}