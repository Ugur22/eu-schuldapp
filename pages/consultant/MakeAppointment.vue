<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Afspraak maken</nb-title>
      </nb-body>
    </nb-header>
    <nb-content padder>
      <nb-card>
        <nb-card-item header>
          <nb-text>Afspraak maken met {{navigation.getParam('firstname')}} {{navigation.getParam('lastname')}} </nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
          <nb-body :style="{ marginBottom: 20}">
            <nb-item floatingLabel>
              <nb-label>Title</nb-label>
              <nb-input v-model="title" />
            </nb-item>
            <nb-item floatingLabel>
              <nb-label>Notes</nb-label>
              <nb-input v-model="notes" />
            </nb-item>
                    <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('Township') }}</nb-label>
          <nb-picker
            mode="dialog"
            placeholder="gender"
            :selectedValue="selectedLocation"
            :onValueChange="onLocationChange"
          >
            <item
              v-for="location in locations"
              :key="location.id"
              :label="location.name"
              :value="location.id"
            />
          </nb-picker>
        </nb-card-item>
          </nb-body>
            <nb-text :style="{ fontSize: 20 }">{{ days[date.getDay()] }}</nb-text>
            <view :style="{ flexDirection:'row' }">
              <nb-text :style="{ fontSize: 35 }">{{ date.toString().substr(4, 12) }}</nb-text>
              <nb-button rounded info :on-press="showDatepicker" :style="{ marginLeft: 'auto' }">
                <nb-icon active name="calendar" />
              </nb-button>
            </view>
            <view :style="{ flexDirection:'row' }">
              <nb-text :style="{ fontSize: 28 }">{{ ('0'  + (date.getHours())).slice(-2) + ":" + ('0'  + (date.getMinutes())).slice(-2) }}</nb-text>
              <nb-button rounded info :on-press="showTimepicker" :style="{ justifyContent: 'center', alignSelf: 'flex-end' }">
                <nb-icon active name="clock" />
              </nb-button>
            </view>
          </nb-body>            
        </nb-card-item>
        <nb-card-item footer :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'center', alignItems: 'center' }">
          <view :style="{ flexDirection:'row' }">
            <nb-button rounded success :on-press="appointmentMake" :style="{marginRight: 5}">
              <nb-icon active name="checkmark" />
            </nb-button>
            <nb-button rounded danger :on-press="appointmentCancel" :style="{marginLeft: 5}">
              <nb-icon active name="close" />
            </nb-button>
          </view>
        </nb-card-item>
      </nb-card>
      <view>
        <date-time-picker
          v-if="show"
          testID="dateTimePicker"
          :value="date"
          :mode="mode"
          :is24Hour=true
          display="default"
          :minimumDate="minimumDate"
          :minuteInterval="10"
          :onChange="getDateTime"
        />
      </view>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/FooterConsultant';
  import DateTimePicker from '@react-native-community/datetimepicker';
  import { Platform } from 'react-native';
  import { Picker,Textarea } from "native-base";
  import { AsyncStorage } from 'react-native';
  
  export default {
    props: {
      navigation: {
        type: Object
      },
      user: {},
    },
    components: { FooterNav, DateTimePicker,Item: Picker.Item },
    data() {
      return {
        date: this.addDays(1),
        mode: 'date',
        show: false,
        minimumDate: this.addDays(1),
        days: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
        title:'',
        notes:'',
        locations: {},
        selectedLocation: '0',
      };
    },
    created() {
        this.getLocations();
    },
    methods: {
    getLocations: async function () {
      try {
        let response = await fetch('http://api.arsus.nl/locations', {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
        });

        let responseJson = await response.json();
        if (responseJson.results) {
          this.locations = responseJson.results;
          this.dataIsReady = true;
        }
      } catch (error) {
        console.log(error);
        console.error(error);
      }
    },
      goBack: function () {
        this.navigation.goBack();
      },
      getDateTime: function (event, selectedDate) {
        let currentDate = selectedDate || date;
        this.date = currentDate;
        this.show = Platform.OS === 'ios';
        
      },
      showMode: function (currentMode) {
        this.show = true;
        this.mode = currentMode;
      },
      showDatepicker: function () {
        this.showMode('date');
      },
      showTimepicker: function () {
        this.showMode('time');
      },
      addDays: function (days) {
        let date = new Date();
        date.setDate(date.getDate() + days);
        return date;
      },
      onLocationChange: function (value) {
      this.selectedLocation = value;
    },
      appointmentMake: async function () {

      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
     
        let formatDate = this.date.getFullYear() + '-' + (this.date.getMonth() + 1) + '-' + this.date.getDate();
        console.log(formatDate);
        let timestamp = this.date.getHours() + ":" + this.date.getMinutes();

      try {
        let response = await fetch('http://api.arsus.nl/consultant/make-appointment', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
            title: this.title,
            notes: this.notes,
            date: formatDate,
            time:timestamp = this.date.getHours() + ":" + this.date.getMinutes(),
            client_id: this.navigation.getParam('ClientID'),
            location_id: this.selectedLocation,
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          console.log('afspraak gemaakt');
          this.navigation.navigate('AppointmentConfirmation', {
            title: this.title,
            notes: this.notes,
            clientName: `${this.navigation.getParam('firstname')} ${this.navigation.getParam('lastname')}`,
            date:  formatDate,
            time: timestamp
          });
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.log(error);
        console.error(error);
      }
        
      },
      appointmentCancel: function () {
        this.navigation.navigate('Appointments');
      }
    }
  }
</script>