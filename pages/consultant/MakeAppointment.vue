<template>
  <nb-container>
      <header :pageTitle="$root.lang.t('make_appointment')" :method="goBack" />
		<Agenda 
			:minDate="formatDateReverse(minimumDate)"
			:renderEmptyDate="renderEmptyDate"
			:renderEmptyData ="renderEmptyData"
			:renderDay ="(day) => renderDay(day)"
			:renderItem="(item,day) => renderItems(item,day)"
			:enableSwipeMonths="true"
			:onDayPress="(day) =>  getSelectedDate(day)"
			:onDayChange="(day) =>  getSelectedDate(day)"
			:items="appointments"
			:style="styleAppointments.agenda"
			:theme="{ 
				backgroundColor:'#fff',
				calendarBackground:'#fff',
				agendaDayTextColor: '#fff',
				dayTextColor:'#0078ae',
				agendaTodayColor:'#0078ae',
				agendaDayNumColor: '#0078ae',
				agendaKnobColor: '#0078ae',
				textSectionTitleColor:'#0078ae',
				selectedDayBackgroundColor: '#0078ae',
				dotColor: '#0078ae',
				dayTextColor: '#2d4150',
  		}"
		/>
    <nb-content padder>
      <nb-card transparent> 
        <nb-card-item header>
          <nb-text>	{{$root.lang.t('client_appoinment')}} {{navigation.getParam('firstname')}} {{navigation.getParam('lastname')}} op {{formatDate(date)}} om {{selectedTime}} </nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
						  <nb-card-item floatingLabel>
					<nb-label>{{$root.lang.t('time')}}</nb-label>
            <nb-picker
              mode="dialog"
              placeholder="tijd"
              :selectedValue="selectedTime"
              :iosIcon="getIosIcon()"
              :onValueChange="onTimeChange">
              <item
                v-for="hour in GetHours()"
                :key="hour"
                :label="hour"
                :value="hour"/>
            </nb-picker>
						  </nb-card-item>
            <nb-item :error="(!title.required)" floatingLabel>
              <nb-label>{{$root.lang.t('title')}}</nb-label>
              <nb-input v-model="title" />
            </nb-item>
            <nb-item floatingLabel :style="{  marginTop:10 }">
              <nb-label>{{$root.lang.t('note')}}</nb-label>
              <nb-input v-model="notes" />
            </nb-item>
            <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('Township') }}:</nb-label>
            <nb-picker
              mode="dialog"
              :placeholder="$root.lang.t('Township')"
              :selectedValue="selectedLocation"
              :iosIcon="getIosIcon()"
              :onValueChange="onLocationChange">
              <item
                v-for="location in locations"
                :key="location.id"
                :label="location.name"
                :value="location.id"/>
            </nb-picker>
        </nb-card-item>
          </nb-body>            
        </nb-card-item>
        <nb-card-item  footer :style="styles.center">
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
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>
  import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
  import { Platform,View,Text,TouchableOpacity,Alert} from 'react-native';
  import { Picker,Icon } from "native-base";
  import {formatDate,FormatTime,formatDay,formatDateReverse} from "../utils/dates";
	import { Toast } from 'native-base';
	import {fetchData} from "../utils/fetch";
	import {GetHours} from "../utils/dates";
	import { Agenda,LocaleConfig} from 'react-native-calendars';
	import React from 'react'
	import {styleAppointments,styles} from '../styling/style';
	import i18n from 'i18n-js';

  export default {
    props: {
      navigation: {
        type: Object
      },
      user: {},
    },
    components: { FooterNav,Item: Picker.Item,Agenda,Header },
    data() {
      return {
        date: this.addDays(0),
        mode: 'date',
        show: false,
        minimumDate: this.addDays(0), 
        title:'',
				notes:'',
				dataIsReady: false,
        locations: {},
				selectedLocation: '4',
				selectedTime: '12:00',
        formatDate,
        FormatTime,
				formatDay,
				GetHours,
				formatDateReverse,
				appointments: {},
				styles,
				styleAppointments,
      };
    },
		mounted() {
			LocaleConfig.locales['nl'] = {
				monthNames: ['januari','februari','maart','april','mei','juni','juli','augustus','September','oktober','November','december'],
				monthNamesShort: ['jan.','feb.','mrt','apr','mei','jun','jul.','aug','sep.','okt.','nov.','dec'],
				dayNames: ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag'],
				dayNamesShort: ['ma','di','wo','do','vr','za','zo'],
				today: 'vandaag'
			};

			LocaleConfig.locales['tr'] = {
				monthNames: ['ocak','şubat','mart','nisan','mayıs','hazīran','temmuz','ağustos','eylül','ekim','kasım','aralık'],
				monthNamesShort: ['oc','şu','ma','ni','may','haz','tem','ağu','ey','eki','kas','ar'],
				dayNames: ['pazartesi','salı','çarşamba','perşembe','cumā','cumartesi','pazar'],
				dayNamesShort: ['pa','sa','ça','pe','cu','cu','pa'],
				today: 'bugün'
			};

			LocaleConfig.locales.en = LocaleConfig.locales[''];

			if(i18n.locale === 'nl'){
					LocaleConfig.defaultLocale = 'nl';
			}
			else if(i18n.locale === 'tr'){
						LocaleConfig.defaultLocale = 'tr';
			}else {
					LocaleConfig.locales.en;
			}
		
			
			fetchData(`locations`,this.$root.user.token).then(val => {
				this.dataIsReady = true; this.locations = val;
			});
			fetchData('appointment-dates',this.$root.user.token).then(val => {
				this.dataIsReady = true; 
				this.appointments = val;
		});
	},
    methods: {
         getIosIcon: function() {
      return <Icon name="arrow-down" />;
    },
      goBack: function () {
        this.navigation.goBack();
			},
			renderEmptyDate: function() {
				return (<View><Text>Text</Text></View>);
			},
			renderDay: function(day) {
				return (
					 <View style={styleAppointments.emptyDate}>
						<Text></Text>
					</View>
				);
			},
			renderEmptyData: function() {
				return (
					 <View style={styleAppointments.emptyDate}>
						<Text>{this.$root.lang.t('no_appointments')}</Text>
					</View>
				);
			},
			renderItems: function(item) {
				return (
      	<TouchableOpacity 
					style={[styleAppointments.item, {height: item.height}]}>
						<View>
							<Text style={{color:'#fff'}}>{item.client}</Text>
							<Text style={{color:'#fff'}}>{item.time}</Text>
							<Text style={{color:'#fff'}}>{item.notes}</Text>
						</View>
					</TouchableOpacity>
				);
			},
      addDays: function (days) {
        let date = new Date();
        date.setDate(date.getDate() + days);
        return date;
			},
			getSelectedDate: function (day){
				this.date = day.dateString;
			},
      onLocationChange: function (value) {
      this.selectedLocation = value;
    },
      onTimeChange: function (value) {
			this.selectedTime = value;
    },
      appointmentMake: async function () {

      if(this.title){
				console.log(this.selectedTime);
        try {
          let response = await fetch('http://api.arsus.nl/consultant/make-appointment', {
            method: 'POST',
            headers: {
              accept: 'application/json',
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${this.$root.user.token}`
            },
            body: JSON.stringify({
              title: this.title,
              notes: this.notes,
              date: this.formatDateReverse(this.date),
              time: this.selectedTime,
              client_id: this.navigation.getParam('ClientID'),
              location_id: this.selectedLocation,
            }),
          });

          let responseJson = await response.json();
					if(responseJson.message === 'appointment existed'){
						Toast.show({
							text: `Er bestaat al een afspraak op deze datum en tijd`,
							position: "center",
							type: "danger",
							duration: 3000, 
						})
					}
          if (responseJson.success) {
            	this.navigation.navigate('AppointmentConfirmation', {
              title: this.title,
              notes: this.notes,
              clientName: `${this.navigation.getParam('firstname')} ${this.navigation.getParam('lastname')}`,
              date: this.formatDate(this.date),
              time: this.selectedTime
            });
          } else {
            console.log(responseJson);
          }
        } catch (error) {
          console.error(error);
        }
      }else {
          Toast.show({
						text: `${this.$root.lang.t('missing_title')}`,
						position: "center",
						type: "danger",
						duration: 3000, 
        })
      }
        
      },
      appointmentCancel: function () {
        this.navigation.navigate('AppointmentsConsultant');
      }
    }
  }
</script>