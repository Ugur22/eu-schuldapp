<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent :on-press="goBack">
          <nb-icon name="arrow-back" />
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
          <nb-body>
            <nb-item :error="(!title.required)" floatingLabel>
              <nb-label>Title</nb-label>
              <nb-input v-model="title" />
            </nb-item>
            <nb-item floatingLabel :style="{  marginTop:10 }">
              <nb-label>Notes</nb-label>
              <nb-input v-model="notes" />
            </nb-item>
            <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('Township') }}:</nb-label>
            <nb-picker
              mode="dialog"
              placeholder="gender"
              :selectedValue="selectedLocation"
              :onValueChange="onLocationChange">
              <item
                v-for="location in locations"
                :key="location.id"
                :label="location.name"
                :value="location.id"/>
            </nb-picker>
        </nb-card-item>
          </nb-body>
            <nb-text :style="{ fontSize: 20 }">{{ formatDay(date)}}</nb-text>
            <view :style="{ flexDirection:'row', paddingTop:10 }">
              <nb-text :style="{ fontSize: 30 }">{{ formatDate(date) }}</nb-text>
              <nb-button rounded info :on-press="showDatepicker" :style="{ marginLeft: 10,backgroundColor:'#0078ae' }">
                <nb-icon active name="calendar" />
              </nb-button>
            </view>
            <view :style="{ flexDirection:'row',paddingTop:10 }">
              <nb-text :style="{ fontSize: 28 }">{{ FormatTime(date)}}</nb-text>
              <nb-button rounded info :on-press="showTimepicker" :style="{ justifyContent: 'center',marginLeft: 10, alignSelf: 'flex-end',backgroundColor:'#0078ae' }">
                <nb-icon active name="clock"/>
              </nb-button>
            </view>
          </nb-body>            
        </nb-card-item>
        <nb-card-item footer :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'center', alignItems: 'center' }">
          <view :style="{ flexDirection:'row' }">
            <nb-button rounded success :on-press="appointmentMake" :style="{marginRight: 5,backgroundColor:'#008551'}">
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
  import FooterNav from '../../included/Footer';
  import DateTimePicker from '@react-native-community/datetimepicker';
  import { Platform } from 'react-native';
  import { Picker,Textarea } from "native-base";
  import AsyncStorage from '@react-native-async-storage/async-storage';
  import moment from "moment";
  import localization from "moment/locale/nl";
  import {formatDate,FormatTime,formatDay} from "../utils/dates";
  import { Toast } from 'native-base';
  
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
        title:'',
        notes:'',
        locations: {},
        selectedLocation: '0',
        formatDate,
        FormatTime,
        formatDay
      };
    },
    created() {
        moment.locale('nl'); 
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

      if(this.title){
        try {
          let response = await fetch('http://api.arsus.nl/consultant/make-appointment', {
            method: 'POST',
            headers: {
              accept: 'application/json',
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${this.user.token}`
            },
            body: JSON.stringify({
              email: this.user.email,
              password: this.user.password,
              title: this.title,
              notes: this.notes,
              date: this.formatDate(this.date),
              time: this.FormatTime(this.date),
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
              date: this.formatDate(this.date),
              time: this.FormatTime(this.date)
            });
          } else {
            console.log(responseJson);
          }
        } catch (error) {
          console.error(error);
        }
      }else {
          Toast.show({
          text: 'vul een titel in!',
          buttonText: 'ok'
        })
      }
        
      },
      appointmentCancel: function () {
        this.navigation.navigate('Appointments');
      }
    }
  }
</script>