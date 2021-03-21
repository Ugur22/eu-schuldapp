<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('appointment')" :method="goBack" />
	<Agenda 
		:renderEmptyDate="renderEmptyDate"
		:renderEmptyData ="renderEmptyData"
		:renderDay ="(day) => renderDay(day)"
		:renderItem="(item,day) => renderItems(item,day)"
		:hideDayNames="false"
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
			dayTextColor: '#2d4150'
		}"
	/>
	<nb-content >
	  <nb-card transparent v-if="dataIsReady">
		<nb-card-item >
		  <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
			<nb-text :style="{ fontSize: 16, color: 'green' }">client: {{firstname}} {{lastname}}</nb-text>
				<nb-item floatingLabel :on-press="showTimepicker" :style="{ marginBottom:10 }">
					<nb-label>{{$root.lang.t('time')}}</nb-label>
					<nb-input disabled v-model="time"  />
			</nb-item>
			<nb-item floatingLabel>
					<nb-label>{{$root.lang.t('date')}}</nb-label>
					<nb-input disabled v-model="SelectedDate"  />
			</nb-item>
      <nb-card-item stackedLabel>
				<nb-label>{{ $root.lang.t('Township') }}:</nb-label>
				<nb-picker :style="{width: Platform.OS === 'android' ? width-120 : width-130}"
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
			      <view>
        <date-time-picker
          v-if="show"
          testID="dateTimePicker"
          :value="date"
          :mode="mode"
          :is24Hour="true"
          display="default"
          :minuteInterval="15"
					:onChange="getDateTime"
        />
      </view>
			<nb-item stackedLabel>
				<nb-label>{{$root.lang.t('note')}}</nb-label>
        <nb-textarea :style="{width: Platform.OS === 'android' ? width-50 : width-40,}" v-model="notes" bordered  placeholder="notes" />
			</nb-item>
		  </nb-body>
		</nb-card-item>
		    <nb-button :style="styles.btn" full :on-press="save">
          <nb-text>save</nb-text>
        </nb-button>
	  </nb-card>
	  <nb-spinner color="#0078ae" v-else /> 
	</nb-content>
	<nb-footer>
	  <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
	</nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/Footer';
	import Header from '../../included/Header';
	import {fetchData} from "../utils/fetch";
	import {formatDate,FormatTime,formatDay,formatDateReverse} from "../utils/dates";
	import { Picker,Icon } from "native-base";
	import React from 'react'
	import {Dimensions,Platform,View,Text,TouchableOpacity,Alert} from 'react-native';
	import { Agenda,LocaleConfig} from 'react-native-calendars';
	import {styleAppointments,styles} from '../styling/style';
	import i18n from 'i18n-js';
	import { Toast } from 'native-base';
	 import DateTimePicker from '@react-native-community/datetimepicker';

  export default {
	props: {
	  navigation: {
			type: Object
	  },
	},
	components: { FooterNav,Header,Item: Picker.Item,Agenda,DateTimePicker },
	data() {
	  return {
		Appointment:{},
		dataIsReady: false,
		formatDate,
		FormatTime,
		styles,
		FormatTime,
		formatDay,
		formatDateReverse,
		notes:'',
		location:'',
		time:'',
		SelectedDate:'',
		date: this.addDays(0),
		minimumDate: this.addDays(0),
		locations: {},
		selectedLocation: '0',
		width: Dimensions.get('window').width,
		Platform,
		firstname: '',
		lastname:'',
		styles,
		styleAppointments,
		appointments: {},
		mode: 'date',
    show: false,
		client_id:0
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

		  fetchData(`consultant/appointment?id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
				this.Appointment = val;
				this.notes = val.notes;
				this.selectedLocation = val.location.id;
				this.location = val.location.name;
				this.time = FormatTime(val.event_date);
				this.firstname = val.client.firstname;
				this.lastname = val.client.lastname;
				this.SelectedDate = formatDate(val.event_date);
				this.client_id = val.client.id;
			});

			fetchData(`locations`,this.$root.user.token).then(val => {
				this.locations = val;
			});

			fetchData('appointment-dates',this.$root.user.token).then(val => {
				this.appointments = val;
				this.dataIsReady = true; 
				
			});
  },
	methods: {
	  goBack: function () {
			this.navigation.goBack();
	  },
    getIosIcon: function() {
      return <Icon name="arrow-down" />;
    },
		renderEmptyDate: function() {
			return (<View><Text>Text</Text></View>);
		},
		renderDay: function(day) {
			return (
				<View style={styleAppointments.emptyDate}>
					{day !== undefined &&
						<Text style={{color:'#0078ae',fontSize:16}}>{day.day}</Text>
					}
				</View>
			);
		},
		renderEmptyData: function() {
			return (
				<View style={{ flex: 1, justifyContent: 'center', alignContent: 'center' }}>
					<Text style={{color:'#0078ae',textAlign: "center", fontSize:18 }}>{this.$root.lang.t('no_appointments')}</Text>
				</View>
			);
		},
		renderItems: function(item,day) {
			return (
			<TouchableOpacity style={[styleAppointments.item, {height: item.height}]}>
				<View>
					<Text style={{color:'#fff'}}>{item.client}</Text>
					<Text style={{color:'#fff'}}>{item.time}</Text>
					<Text style={{color:'#fff'}}>{item.notes}</Text>
				</View>
			</TouchableOpacity>
			);
		},
		save : async function () {
			console.log(this.SelectedDate);
			console.log(this.time);
			try {

				let response = await fetch(`http://api.arsus.nl/consultant/appointment/update/${this.navigation.getParam('id')}`, {
					method: 'POST',
					headers: {
						accept: 'application/json',
						'Content-Type': 'application/json',
						'Authorization': `Bearer ${this.$root.user.token}`
					},
					body: JSON.stringify({
						notes: this.notes,
						date: this.SelectedDate,
						time: this.time,
						client_id: this.client_id,
						location_id: this.selectedLocation,
					}),
				});

				let responseJson = await response.json();
				// if(responseJson.message === 'appointment existed'){
				// 	Toast.show({
				// 		text: `Er bestaat al een afspraak op deze datum en tijd`,
				// 		position: "center",
				// 		type: "danger",
				// 		duration: 3000, 
				// 	})
				// }
				if (responseJson.success) {
						console.log('success');
				} else {
					console.log(responseJson);
				}
			} catch (error) {
				console.error(error);
			}
	  },
      addDays: function (days) {
        let date = new Date();
        date.setDate(date.getDate() + days);
        return date;
			},
			getSelectedDate: function (day){
				console.log(day.dateString);
				this.SelectedDate = this.formatDate(day.dateString);
			},
			onLocationChange: function (value) {
      this.selectedLocation = value;
    	},
      getDateTime: function (event, selectedDate) {
        let currentDate = selectedDate;
        this.show = Platform.OS === 'ios';
				this.time = this.FormatTime(currentDate); 
				
				console.log(this.time);       
      },
      showMode: function (currentMode) {
        this.show = true;
        this.mode = currentMode;
      },
      showTimepicker: function () {
        this.showMode('time');
      },
	}
  }
</script>
<style scoped>

</style>