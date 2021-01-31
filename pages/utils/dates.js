import moment from "moment";
moment.locale('nl');

	export function formatDate(date) {
    let Formatdate = moment(date).format("DD-MM-YYYY");
    return Formatdate;
	}
	
	export function formatDateReverse(date) {
    let Formatdate = moment(date).format("YYYY-MM-DD");
    return Formatdate;
  }

  export function FormatTime(date) {
    let FormatTime = moment(date).format("HH:mm");
    return FormatTime;
  }
  
  export function formatDay (date) {
    let formatDay = moment(date).format("dddd");
    return formatDay;
	}
	
	export function GetHours(){
		return Array.from({length: 24}, (_,i) => i).reduce((r,hour) => {
			r.push(moment({hour, minute: 0}).format('HH:mm'));
			return r;
		}, []);
	}
  