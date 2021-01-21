import AsyncStorage from '@react-native-async-storage/async-storage';

export async function fetchData(url,datatype='') {

		let responseJson;
		let value = '';
		let user = {};
    try {
      value = await AsyncStorage.getItem('login');
      user = JSON.parse(value);
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
           'Authorization': `Bearer ${user.token}`
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