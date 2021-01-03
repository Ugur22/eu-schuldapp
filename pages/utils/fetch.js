import AsyncStorage from '@react-native-async-storage/async-storage';

export async function fetchData(user,url,dataReady,collection) {
    let value = '';
    try {
      value = await AsyncStorage.getItem('login');
      user = JSON.parse(value);
    } catch (error) {
      // Error retrieving data
      console.log(error.message);
    }

    try {
      let response = await fetch(url, {
        method: 'GET',
        headers: {
          accept: 'application/json',
          'Content-Type': 'application/json',
           'Authorization': `Bearer ${user.token}`
        },
      });

      let responseJson = await response.json();
      if (responseJson.success) {
        collection = responseJson.results;
        dataReady = true;

        return collection;
      } else {
        console.log(responseJson);
      }
    } catch (error) {
      console.error(error);
    }

}