import AsyncStorage from '@react-native-async-storage/async-storage';

export async function fetchData(url,usertoken,datatype='') {

		let responseJson;

		let value = '';
		try {
			value = await usertoken;

		} catch (error) {
			// Error retrieving data
			console.log(error.message);
		}

    try {
      let response = await fetch(`http://api.arsus.nl/${url}/`, {
        method: 'GET',
        headers: {
          accept: 'application/json',
          'Content-Type': 'application/json',
           'Authorization': `Bearer ${usertoken}`
        },
      });

			if(datatype == 'file'){
				responseJson = await response.text();
			}else {
				responseJson = await response.json();
			}
      if (responseJson.success ? responseJson.success : responseJson) {

        return responseJson.results ?  responseJson.results : responseJson;
      } else {
        console.log(responseJson);
      }
    } catch (error) {
      console.error(error);
    }
}

export async function PostData(url,usertoken) {
	let value = '';
	try {
		value = await usertoken;

	} catch (error) {
		// Error retrieving data
		console.log(error.message);
	}

	try {
		let response = await fetch(`http://api.arsus.nl/${url}/`, {
			method: 'POST',
			headers: {
			accept: 'application/json',
			'Authorization': `Bearer ${usertoken}`
			}
		});
		let responseJson = await response.json();
		if (responseJson.success) {
			return responseJson
		} else {
			console.log(responseJson);
			}
	} catch (error) {
		console.log(error);
		console.error(error);
	}
}

export async function getUser() {
	let value = '';
	let user = {};
	try {
		value = await AsyncStorage.getItem('login');
		user = JSON.parse(value);

		return user;
	} catch (error) {
		// Error retrieving data
		console.log(error.message);
	}
}