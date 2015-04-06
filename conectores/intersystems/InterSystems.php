<?php
class SendEvent {
  public $pRequest; // Event
}

class Event {
  public $ControlID; // String
  public $CorrelatedControlID; // String
  public $OperatorCode; // OperatorCode
  public $RecordedDateTime; // TimeStamp
  public $MessageName; // String
  public $SpecificationCode; // anonymous3
  public $OriginatingSystemCode; // SystemCode
  public $EventTime; // TimeStamp
  public $SendingLocationCode; // anonymous4
  public $SendingFacilityCode; // anonymous5
}

class OperatorCode {
}

class anonymous3 {
}

class anonymous4 {
}

class anonymous5 {
}

class String {
}

class SystemCode {
}

class TimeStamp {
}

class ApptConfirmationEvent {
  public $ReferralDocumentNumber; // ReferralDocumentNumber
  public $AppointmentId; // AppointmentId
  public $ConfirmationCode; // ConfirmationCode
}

class ReferralDocumentNumber {
}

class AppointmentId {
}

class ConfirmationCode {
}

class CreateMREvent {
  public $PatientNationalId; // NationalId
  public $RegistrationNumber; // RegistrationNumber
  public $MedicalRecord; // MedicalRecord
}

class RegistrationNumber {
}

class NationalId {
}

class MedicalRecord {
  public $OldMRN; // OldMRN
  public $MRN; // MRN
  public $MRTypeCode; // MRTypeCode
  public $CreationDate; // TimeStamp
  public $CreationUserNationalId; // CreationUserNationalId
  public $HomeFacilityCode; // HomeFacilityCode
  public $HomeLocationCode; // HomeLocationCode
}

class OldMRN {
}

class MRN {
}

class MRTypeCode {
}

class CreationUserNationalId {
}

class HomeFacilityCode {
}

class HomeLocationCode {
}

class PatientAdmDischargeEvent {
  public $ReferralDocumentNumber; // ReferralDocumentNumber
  public $Patient; // Patient
  public $AdmInfo; // AdmInfo
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $DischargeInfo; // DischargeInfo
  public $Diagnosis; // ArrayOfDiagnosisDiagnosis
}

//class ReferralDocumentNumber {
//}

class Patient {
  public $NationalId; // NationalId
  public $PRAIS; // Boolean
  public $RegistrationNumber; // RegistrationNumber
  public $PatientTypeCode; // PatientTypeCode
  public $PassportNumber; // PassportNumber
  public $GivenName; // GivenName
  public $FamilyName; // FamilyName
  public $SecondaryName; // SecondaryName
  public $SexCode; // SexCode
  public $MaritalStatusCode; // MaritalStatusCode
  public $MaritalStatusDesc; // MaritalStatusDesc
  public $NationalityCode; // NationalityCode
  public $NationalityDesc; // NationalityDesc
  public $ReligionCode; // ReligionCode
  public $ReligionDesc; // ReligionDesc
  public $DateOfBirth; // Date
  public $TimeOfBirth; // Time
  public $HomeAddressStreet; // HomeAddressStreet
  public $HomeAddressCityCode; // HomeAddressCityCode
  public $HomeAddressCityDesc; // HomeAddressCityDesc
  public $HomeAddressCityAreaCode; // HomeAddressCityAreaCode
  public $HomeAddressCityAreaDesc; // HomeAddressCityAreaDesc
  public $HomeAddressProvinceCode; // HomeAddressProvinceCode
  public $HomeAddressProvinceDesc; // HomeAddressProvinceDesc
  public $HomeAddressRegionCode; // HomeAddressRegionCode
  public $HomeAddressRegionDesc; // HomeAddressRegionDesc
  public $HomePhone; // HomePhone
  public $MobilePhone; // MobilePhone
  public $EMail; // EMail
  public $WorkPhone; // WorkPhone
  public $Remarks; // Remarks
  public $EthnicGroupCode; // EthnicGroupCode
  public $EducationCode; // EducationCode
  public $OccupationCode; // OccupationCode
  public $ParentNationalId; // NationalId
}

//class RegistrationNumber {
//}

class PatientTypeCode {
}

class PassportNumber {
}

class GivenName {
}

class FamilyName {
}

class SecondaryName {
}

class SexCode {
}

class MaritalStatusCode {
}

class MaritalStatusDesc {
}

class NationalityCode {
}

class NationalityDesc {
}

class ReligionCode {
}

class ReligionDesc {
}

class HomeAddressStreet {
}

class HomeAddressCityCode {
}

class HomeAddressCityDesc {
}

class HomeAddressCityAreaCode {
}

class HomeAddressCityAreaDesc {
}

class HomeAddressProvinceCode {
}

class HomeAddressProvinceDesc {
}

class HomeAddressRegionCode {
}

class HomeAddressRegionDesc {
}

class HomePhone {
}

class MobilePhone {
}

class EMail {
}

class WorkPhone {
}

class Remarks {
}

class EthnicGroupCode {
}

class EducationCode {
}

class OccupationCode {
}

class Boolean {
}

class Date {
}

class Time {
}

class AdmInfo {
  public $Type; // Type
  public $VisitStatus; // VisitStatus
  public $EpisodeNumber; // EpisodeNumber
  public $AssignedPatientFacilityCode; // AssignedPatientFacilityCode
  public $AssignedPatientLocationCode; // AssignedPatientLocationCode
  public $AttendingDoctorNationalId; // NationalId
  public $AdmissionDateTime; // TimeStamp
  public $AdmissionCreationDateTime; // TimeStamp
  public $CauseOfInjuryCode; // CauseOfInjuryCode
  public $CurrentWardCode; // CurrentWardCode
  public $CurrentRoomCode; // CurrentRoomCode
  public $CurrentBedCode; // CurrentBedCode
  public $LocCode; // LocCode
  public $Remarks; // Remarks
  public $PriorityCode; // PriorityCode
}

class Type {
}

class VisitStatus {
}

class EpisodeNumber {
}

class AssignedPatientFacilityCode {
}

class AssignedPatientLocationCode {
}

class CauseOfInjuryCode {
}

class CurrentWardCode {
}

class CurrentRoomCode {
}

class CurrentBedCode {
}

class LocCode {
}

//class Remarks {
//}

class PriorityCode {
}

class DischargeInfo {
  public $DischargeDateTime; // TimeStamp
  public $ClinicalDischargeDateTime; // TimeStamp
  public $EstimDischargeDateTime; // TimeStamp
  public $DischargeDoctorNationalId; // NationalId
}

class Diagnosis {
  public $DiagnosisCode; // DiagnosisCode
  public $DiagnosisDescription; // DiagnosisDescription
  public $DiagnosisRemarks; // DiagnosisRemarks
  public $DiagnosisTypeCode; // DiagnosisTypeCode
  public $DiagnosisTypeDesc; // DiagnosisTypeDesc
}

class DiagnosisCode {
}

class DiagnosisDescription {
}

class DiagnosisRemarks {
}

class DiagnosisTypeCode {
}

class DiagnosisTypeDesc {
}

class PatientAdmEvent {
  public $Patient; // Patient
  public $AdmInfo; // AdmInfo
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
}

class Insurance {
  public $PayorCode; // PayorCode
  public $PayorNationalId; // NationalId
  public $PayorDesc; // PayorDesc
  public $PlanCode; // PlanCode
  public $PlanDesc; // PlanDesc
  public $HolderNationalId; // NationalId
  public $InstCode; // InstCode
  public $RelationshipToHolder; // RelationshipToHolder
  public $IsActive; // Boolean
  public $Message; // Message
}

class PayorCode {
}

class PayorDesc {
}

class PlanCode {
}

class PlanDesc {
}

class InstCode {
}

class RelationshipToHolder {
}

class Message {
}

class HealthCareProvider {
  public $AreaCode; // AreaCode
  public $Code; // Code
  public $Address; // Address
  public $Phone; // Phone
  public $DateOfEntry; // Date
}

class AreaCode {
}

class Code {
}

class Address {
}

class Phone {
}

class PatientAdmUpdEvent {
  public $Patient; // Patient
  public $AdmInfo; // AdmInfo
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
}

class PatientApptCancelEvent {
  public $Patient; // Patient
  public $ApptInfo; // ApptInfo
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
}

class ApptInfo {
  public $ApptDateAndTime; // TimeStamp
  public $ServiceCode; // ServiceCode
  public $ServiceDesc; // ServiceDesc
  public $PersonResId; // NationalId
  public $PersonResSurnames; // String
  public $PersonResGivenName; // String
  public $EpisodeNumber; // EpisodeNumber
  public $ApptFacilityCode; // ApptFacilityCode
  public $ApptLocationCode; // ApptLocationCode
}

class ServiceCode {
}

class ServiceDesc {
}

//class EpisodeNumber {
//}

class ApptFacilityCode {
}

class ApptLocationCode {
}

class PatientApptEvent {
  public $ControlID; // String
  public $CorrelatedControlID; // String
  public $SessionTypeCode; // TimeStamp
  public $AppointmentId; // AppointmentId
  public $StatusCode; // StatusCode
  public $StatusDesc; // StatusDesc
  public $ReferralDocumentNumber; // ReferralDocumentNumber
  public $IsConfirmed; // Boolean
  public $OperatorCode; // OperatorCode
  public $RecordedDateTime; // TimeStamp
  public $Patient; // Patient
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $Insurances; // ArrayOfInsuranceInsurance
  public $Services; // ArrayOfApptServiceInfoApptServiceInfo
  public $Personnel; // ArrayOfApptPersonInfoApptPersonInfo
  public $MessageName; // String
  public $SpecificationCode; // String
  public $OriginatingSystemCode; // SystemCode
  public $EventTime; // TimeStamp
  public $SendingLocationDesc; // anonymous110
  public $SendingFacilityDesc; // anonymous111
  public $SendingLocationCode; // anonymous112
  public $SendingFacilityCode; // anonymous113
}

//class AppointmentId {
//}

class StatusCode {
}

class StatusDesc {
}

//class ReferralDocumentNumber {
//}

//class OperatorCode {
//}

class anonymous110 {
}

class anonymous111 {
}

class anonymous112 {
}

class anonymous113 {
}

class ApptServiceInfo {
  public $ApptDateAndTime; // TimeStamp
  public $ServiceCode; // ServiceCode
  public $ServiceDesc; // ServiceDesc
}

//class ServiceCode {
//}

//class ServiceDesc {
//}

class ApptPersonInfo {
  public $PersonResId; // NationalId
  public $PersonResSurnames; // PersonResSurnames
  public $PersonResGivenName; // PersonResGivenName
}

class PersonResSurnames {
}

class PersonResGivenName {
}

class PatientApptNSPEvent {
  public $ReasonForNotShowCode; // ReasonForNotShowCode
  public $ReasonForNotShowDesc; // ReasonForNotShowDesc
  public $Patient; // Patient
  public $ApptInfo; // ApptInfo
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
}

class ReasonForNotShowCode {
}

class ReasonForNotShowDesc {
}

class PatientApptUpdEvent {
  public $ReasonCode; // ReasonCode
  public $ReasonDesc; // ReasonDesc
}

class ReasonCode {
}

class ReasonDesc {
}

class PatientBillingCancelEvent {
  public $BillNumber; // BillNumber
  public $EpisodeNumber; // EpisodeNumber
}

class BillNumber {
}

//class EpisodeNumber {
//}

class PatientBillingEvent {
  public $Patient; // Patient
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $Insurances; // ArrayOfInsuranceInsurance
  public $Responsible; // BillingResponsible
  public $BillingHeader; // BillingHeader
  public $BillingItems; // BillingItem
}

class BillingResponsible {
}

class NOK {
  public $WorkPhone; // String
  public $EMail; // String
  public $NationalId; // NationalId
  public $GivenName; // GivenName
  public $FamilyName; // FamilyName
  public $SecondaryName; // SecondaryName
  public $Relation; // Relation
  public $HomeAddressStreet; // HomeAddressStreet
  public $HomeAddressCityCode; // HomeAddressCityCode
  public $HomeAddressCityDesc; // HomeAddressCityDesc
  public $HomePhone; // HomePhone
  public $MobilePhone; // MobilePhone
}

//class GivenName {
//}

//class FamilyName {
//}

//class SecondaryName {
//}

class Relation {
}

/*class HomeAddressStreet {
}

class HomeAddressCityCode {
}

class HomeAddressCityDesc {
}

class HomePhone {
}

class MobilePhone {
}*/

class BillingHeader {
  public $BillNumber; // BillNumber
  public $EpisodeType; // EpisodeType
  public $EpisodeNumber; // EpisodeNumber
  public $AdmissionDateTime; // TimeStamp
  public $DischargeDateTime; // TimeStamp
  public $ClinicalDischargeDateTime; // TimeStamp
  public $BillOpeningDateTime; // TimeStamp
  public $BillClosingDateTime; // TimeStamp
  public $HealthCareProvider; // HealthCareProvider
}

/*class BillNumber {
}

class EpisodeType {
}

class EpisodeNumber {
}*/

class BillingItem {
  public $LocationCode; // LocationCode
  public $LocationDesc; // LocationDesc
  public $ItemUseDateTime; // TimeStamp
  public $ItemCode; // ItemCode
  public $ItemDesc; // ItemDesc
  public $ItemGroupCode; // ItemGroupCode
  public $ItemGroupDesc; // ItemGroupDesc
  public $ItemTotalAmount; // Numeric
  public $ItemQuantity; // Numeric
  public $ItemUnityOfMeasureCode; // ItemUnityOfMeasureCode
  public $ItemUnityOfMeasureDesc; // ItemUnityOfMeasureDesc
  public $AmountForPatient; // Numeric
  public $Insurance1PayorCode; // Insurance1PayorCode
  public $AmountForInsurance1; // Numeric
  public $Insurance2PayorCode; // Insurance2PayorCode
  public $AmountForInsurance2; // Numeric
  public $Insurance3PayorCode; // Insurance3PayorCode
  public $AmountForInsurance3; // Numeric
  public $Insurance4PayorCode; // Insurance4PayorCode
  public $AmountForInsurance4; // Numeric
}

class LocationCode {
}

class LocationDesc {
}

class ItemCode {
}

class ItemDesc {
}

class ItemGroupCode {
}

class ItemGroupDesc {
}

class ItemUnityOfMeasureCode {
}

class ItemUnityOfMeasureDesc {
}

class Insurance1PayorCode {
}

class Insurance2PayorCode {
}

class Insurance3PayorCode {
}

class Insurance4PayorCode {
}

class Numeric {
}

class PatientBillingPaidEvent {
  public $BillNumber; // BillNumber
  public $EpisodeNumber; // EpisodeNumber
}

/*class BillNumber {
}

class EpisodeNumber {
}*/

class PatientEvent {
  public $Patient; // Patient
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
}

class PatientMovementEvent {
  public $Patient; // Patient
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $AdmInfo; // AdmInfo
  public $MovInfo; // MovInfo
}

class MovInfo {
  public $MovementDateTime; // TimeStamp
}

class PatientPreAdmEvent {
  public $Patient; // Patient
  public $AdmInfo; // AdmInfo
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
}

class PatientUpdEvent {
}

class ProcedureReferralEvent {
  public $Patient; // Patient
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
  public $RequestingCareProvider; // CareProvider
  public $ReferralInfo; // ProcedureReferralInfo
}

class CareProvider {
  public $NationalId; // NationalId
  public $GivenName; // GivenName
  public $FamilyName; // FamilyName
  public $SecondaryName; // SecondaryName
  public $HomeAddressStreet; // HomeAddressStreet
  public $HomeAddressCityCode; // HomeAddressCityCode
  public $HomeAddressCityDesc; // HomeAddressCityDesc
  public $HomePhone; // HomePhone
  public $MobilePhone; // MobilePhone
  public $WorkPhone; // WorkPhone
  public $EMail; // EMail
}

/*class GivenName {
}*/

/*class FamilyName {
}

class SecondaryName {
}

class HomeAddressStreet {
}

class HomeAddressCityCode {
}

class HomeAddressCityDesc {
}

class HomePhone {
}

class MobilePhone {
}

class WorkPhone {
}

class EMail {
}*/

class ProcedureReferralInfo {
  public $OriginatingReferralCode; // String
  public $ReferralReasonCode; // ReferralReasonCode
  public $OriginatingRegionCode; // OriginatingRegionCode
  public $OriginatingSpecialtyCode; // OriginatingSpecialtyCode
  public $OriginatingFacilityCode; // OriginatingFacilityCode
  public $DiagnosisCode; // DiagnosisCode
  public $DiagnosisControlLevel; // Numeric
  public $DiagnosisRemarks; // DiagnosisRemarks
  public $EpisodePriorityCode; // EpisodePriorityCode
  public $EpisodeComplexityLevelCode; // EpisodeComplexityLevelCode
  public $Remarks; // Remarks
  public $GeneralData; // GeneralData
  public $RequestedProcedureCode; // RequestedProcedureCode
}

class ReferralReasonCode {
}

class OriginatingRegionCode {
}

class OriginatingSpecialtyCode {
}

class OriginatingFacilityCode {
}

/*class DiagnosisCode {
}

class DiagnosisRemarks {
}*/

class EpisodePriorityCode {
}

class EpisodeComplexityLevelCode {
}

//class Remarks {
//}

class GeneralData {
}

class RequestedProcedureCode {
}

class ReferralDischargeEvent {
  public $ReferralDocumentNumber; // ReferralDocumentNumber
  public $ReasonCode; // ReasonCode
}

//class ReferralDocumentNumber {
//}

//class ReasonCode {
//}

class ReferralEvent {
  public $Patient; // Patient
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
  public $Insurances; // ArrayOfInsuranceInsurance
  public $RequestingCareProvider; // CareProvider
}

class ReferralResponseEvent {
  public $ReferralDocumentNumber; // ReferralDocumentNumber
  public $DestinationRegionCode; // DestinationRegionCode
  public $DestinationFacilityCode; // DestinationFacilityCode
  public $DestinationLocationCode; // DestinationLocationCode
  public $DestinationLocationDescription; // DestinationLocationDescription
  public $DestinationSpecialtyCode; // DestinationSpecialtyCode
}

//class ReferralDocumentNumber {
//}

class DestinationRegionCode {
}

class DestinationFacilityCode {
}

class DestinationLocationCode {
}

class DestinationLocationDescription {
}

class DestinationSpecialtyCode {
}

class ResponseEvent {
  public $IsError; // Boolean
  public $ErrorDescription; // ErrorDescription
}

class ErrorDescription {
}

class SendConfigEvent {
  public $ConfigCode; // ConfigCode
  public $ConfigValue; // ConfigValue
}

class ConfigCode {
}

class ConfigValue {
}

class SpecialtyReferralEvent {
  public $ReferralInfo; // SpecialtyReferralInfo
}

class SpecialtyReferralInfo {
  public $OriginatingReferralCode; // String
  public $ReferralReasonCode; // ReferralReasonCode
  public $OriginatingRegionCode; // OriginatingRegionCode
  public $OriginatingSpecialtyCode; // OriginatingSpecialtyCode
  public $DiagnosisCode; // DiagnosisCode
  public $DiagnosisControlLevel; // Numeric
  public $DiagnosisRemarks; // DiagnosisRemarks
  public $EpisodePriorityCode; // EpisodePriorityCode
  public $EpisodeComplexityLevelCode; // EpisodeComplexityLevelCode
  public $Remarks; // Remarks
  public $GeneralData; // GeneralData
  public $RequestedSpecialtyCode; // RequestedSpecialtyCode
}

//class ReferralReasonCode {
//}

//class OriginatingRegionCode {
//}

/*class OriginatingSpecialtyCode {
}

class DiagnosisCode {
}

class DiagnosisRemarks {
}

class EpisodePriorityCode {
}

class EpisodeComplexityLevelCode {
}

class Remarks {
}

class GeneralData {
}*/

class RequestedSpecialtyCode {
}

class WLEntryEvent {
  public $WLEntryCode; // WLEntryCode
  public $Patient; // Patient
  public $Insurances; // ArrayOfInsuranceInsurance
  public $MedicalRecords; // ArrayOfMedicalRecordMedicalRecord
  public $HealthCareProvider; // HealthCareProvider
  public $WLEntry; // WLEntry
}

class WLEntryCode {
}

class WLEntry {
  public $DiagnosisCode; // DiagnosisCode
  public $DiagnosisCodingSystem; // DiagnosisCodingSystem
  public $Remarks; // Remarks
  public $DiagnosisRemarks; // DiagnosisRemarks
  public $ReferralReasonCode; // ReferralReasonCode
  public $ReferralDate; // TimeStamp
  public $GeneralData; // GeneralData
  public $OriginatingRegionCode; // OriginatingRegionCode
  public $SpecialtyCode; // SpecialtyCode
  public $LocationCode; // LocationCode
  public $FacilityCode; // FacilityCode
  public $DiagnosisControlLevel; // Numeric
  public $EpisodeComplexityLevelCode; // EpisodeComplexityLevelCode
  public $EpisodePriorityCode; // EpisodePriorityCode
}

/*class DiagnosisCode {
}

class DiagnosisCodingSystem {
}

class Remarks {
}

class DiagnosisRemarks {
}

class ReferralReasonCode {
}

class GeneralData {
}

class OriginatingRegionCode {
}

class SpecialtyCode {
}

class LocationCode {
}

class FacilityCode {
}

class EpisodeComplexityLevelCode {
}

class EpisodePriorityCode {
}*/

class WLStatusUpdEvent {
  public $WLEntryCode; // WLEntryCode
  public $StatusCode; // StatusCode
  public $ReasonCode; // ReasonCode
}

/*class WLEntryCode {
}

class StatusCode {
}

class ReasonCode {
}*/

class SendEventResponse {
  public $SendEventResult; // string
  public $pResponse; // Event
}

class SendQuery {
  public $pQuery; // Query
}

class InsuranceQueryReq {
  public $PatientNationalID; // NationalId
  public $Probing; // boolean
}

class Query {
}

/*class Message {
  public $ControlID; // ControlID
  public $CorrelatedControlID; // CorrelatedControlID
  public $SpecificationCode; // anonymous247
  public $MessageName; // anonymous248
  public $OriginatingSystemCode; // SystemCode
  public $EventTime; // TimeStamp
  public $SendingLocationCode; // anonymous249
  public $SendingFacilityCode; // anonymous250
}*/

class ControlID {
}

class CorrelatedControlID {
}

class anonymous247 {
}

class anonymous248 {
}

class anonymous249 {
}

class anonymous250 {
}

class InsuranceQueryRes {
  public $Patient; // Patient
  public $Insurances; // ArrayOfInsuranceInsurance
  public $ErrorCode; // long
  public $ErrorMessage; // ErrorMessage
}

class ErrorMessage {
}

class SendQueryResponse {
  public $SendQueryResult; // string
  public $pQueryResp; // Query
}

class TestConnection {
}

class TestConnectionResponse {
  public $TestConnectionResult; // string
}


/**
 * InterSystems class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class InterSystems extends SoapServer {

  private static $classmap = array(
                                    'SendEvent' => 'SendEvent',
                                    'Event' => 'Event',
                                    'OperatorCode' => 'OperatorCode',
                                    'anonymous3' => 'anonymous3',
                                    'anonymous4' => 'anonymous4',
                                    'anonymous5' => 'anonymous5',
                                    'String' => 'String',
                                    'SystemCode' => 'SystemCode',
                                    'TimeStamp' => 'TimeStamp',
                                    'ApptConfirmationEvent' => 'ApptConfirmationEvent',
                                    'ReferralDocumentNumber' => 'ReferralDocumentNumber',
                                    'AppointmentId' => 'AppointmentId',
                                    'ConfirmationCode' => 'ConfirmationCode',
                                    'CreateMREvent' => 'CreateMREvent',
                                    'RegistrationNumber' => 'RegistrationNumber',
                                    'NationalId' => 'NationalId',
                                    'MedicalRecord' => 'MedicalRecord',
                                    'OldMRN' => 'OldMRN',
                                    'MRN' => 'MRN',
                                    'MRTypeCode' => 'MRTypeCode',
                                    'CreationUserNationalId' => 'CreationUserNationalId',
                                    'HomeFacilityCode' => 'HomeFacilityCode',
                                    'HomeLocationCode' => 'HomeLocationCode',
                                    'PatientAdmDischargeEvent' => 'PatientAdmDischargeEvent',
                                    'ReferralDocumentNumber' => 'ReferralDocumentNumber',
                                    'Patient' => 'Patient',
                                    'RegistrationNumber' => 'RegistrationNumber',
                                    'PatientTypeCode' => 'PatientTypeCode',
                                    'PassportNumber' => 'PassportNumber',
                                    'GivenName' => 'GivenName',
                                    'FamilyName' => 'FamilyName',
                                    'SecondaryName' => 'SecondaryName',
                                    'SexCode' => 'SexCode',
                                    'MaritalStatusCode' => 'MaritalStatusCode',
                                    'MaritalStatusDesc' => 'MaritalStatusDesc',
                                    'NationalityCode' => 'NationalityCode',
                                    'NationalityDesc' => 'NationalityDesc',
                                    'ReligionCode' => 'ReligionCode',
                                    'ReligionDesc' => 'ReligionDesc',
                                    'HomeAddressStreet' => 'HomeAddressStreet',
                                    'HomeAddressCityCode' => 'HomeAddressCityCode',
                                    'HomeAddressCityDesc' => 'HomeAddressCityDesc',
                                    'HomeAddressCityAreaCode' => 'HomeAddressCityAreaCode',
                                    'HomeAddressCityAreaDesc' => 'HomeAddressCityAreaDesc',
                                    'HomeAddressProvinceCode' => 'HomeAddressProvinceCode',
                                    'HomeAddressProvinceDesc' => 'HomeAddressProvinceDesc',
                                    'HomeAddressRegionCode' => 'HomeAddressRegionCode',
                                    'HomeAddressRegionDesc' => 'HomeAddressRegionDesc',
                                    'HomePhone' => 'HomePhone',
                                    'MobilePhone' => 'MobilePhone',
                                    'EMail' => 'EMail',
                                    'WorkPhone' => 'WorkPhone',
                                    'Remarks' => 'Remarks',
                                    'EthnicGroupCode' => 'EthnicGroupCode',
                                    'EducationCode' => 'EducationCode',
                                    'OccupationCode' => 'OccupationCode',
                                    'Boolean' => 'Boolean',
                                    'Date' => 'Date',
                                    'Time' => 'Time',
                                    'AdmInfo' => 'AdmInfo',
                                    'Type' => 'Type',
                                    'VisitStatus' => 'VisitStatus',
                                    'EpisodeNumber' => 'EpisodeNumber',
                                    'AssignedPatientFacilityCode' => 'AssignedPatientFacilityCode',
                                    'AssignedPatientLocationCode' => 'AssignedPatientLocationCode',
                                    'CauseOfInjuryCode' => 'CauseOfInjuryCode',
                                    'CurrentWardCode' => 'CurrentWardCode',
                                    'CurrentRoomCode' => 'CurrentRoomCode',
                                    'CurrentBedCode' => 'CurrentBedCode',
                                    'LocCode' => 'LocCode',
                                    'Remarks' => 'Remarks',
                                    'PriorityCode' => 'PriorityCode',
                                    'DischargeInfo' => 'DischargeInfo',
                                    'Diagnosis' => 'Diagnosis',
                                    'DiagnosisCode' => 'DiagnosisCode',
                                    'DiagnosisDescription' => 'DiagnosisDescription',
                                    'DiagnosisRemarks' => 'DiagnosisRemarks',
                                    'DiagnosisTypeCode' => 'DiagnosisTypeCode',
                                    'DiagnosisTypeDesc' => 'DiagnosisTypeDesc',
                                    'PatientAdmEvent' => 'PatientAdmEvent',
                                    'Insurance' => 'Insurance',
                                    'PayorCode' => 'PayorCode',
                                    'PayorDesc' => 'PayorDesc',
                                    'PlanCode' => 'PlanCode',
                                    'PlanDesc' => 'PlanDesc',
                                    'InstCode' => 'InstCode',
                                    'RelationshipToHolder' => 'RelationshipToHolder',
                                    'Message' => 'Message',
                                    'HealthCareProvider' => 'HealthCareProvider',
                                    'AreaCode' => 'AreaCode',
                                    'Code' => 'Code',
                                    'Address' => 'Address',
                                    'Phone' => 'Phone',
                                    'PatientAdmUpdEvent' => 'PatientAdmUpdEvent',
                                    'PatientApptCancelEvent' => 'PatientApptCancelEvent',
                                    'ApptInfo' => 'ApptInfo',
                                    'ServiceCode' => 'ServiceCode',
                                    'ServiceDesc' => 'ServiceDesc',
                                    'EpisodeNumber' => 'EpisodeNumber',
                                    'ApptFacilityCode' => 'ApptFacilityCode',
                                    'ApptLocationCode' => 'ApptLocationCode',
                                    'PatientApptEvent' => 'PatientApptEvent',
                                    'AppointmentId' => 'AppointmentId',
                                    'StatusCode' => 'StatusCode',
                                    'StatusDesc' => 'StatusDesc',
                                    'ReferralDocumentNumber' => 'ReferralDocumentNumber',
                                    'OperatorCode' => 'OperatorCode',
                                    'anonymous110' => 'anonymous110',
                                    'anonymous111' => 'anonymous111',
                                    'anonymous112' => 'anonymous112',
                                    'anonymous113' => 'anonymous113',
                                    'ApptServiceInfo' => 'ApptServiceInfo',
                                    'ServiceCode' => 'ServiceCode',
                                    'ServiceDesc' => 'ServiceDesc',
                                    'ApptPersonInfo' => 'ApptPersonInfo',
                                    'PersonResSurnames' => 'PersonResSurnames',
                                    'PersonResGivenName' => 'PersonResGivenName',
                                    'PatientApptNSPEvent' => 'PatientApptNSPEvent',
                                    'ReasonForNotShowCode' => 'ReasonForNotShowCode',
                                    'ReasonForNotShowDesc' => 'ReasonForNotShowDesc',
                                    'PatientApptUpdEvent' => 'PatientApptUpdEvent',
                                    'ReasonCode' => 'ReasonCode',
                                    'ReasonDesc' => 'ReasonDesc',
                                    'PatientBillingCancelEvent' => 'PatientBillingCancelEvent',
                                    'BillNumber' => 'BillNumber',
                                    'EpisodeNumber' => 'EpisodeNumber',
                                    'PatientBillingEvent' => 'PatientBillingEvent',
                                    'BillingResponsible' => 'BillingResponsible',
                                    'NOK' => 'NOK',
                                    'GivenName' => 'GivenName',
                                    'FamilyName' => 'FamilyName',
                                    'SecondaryName' => 'SecondaryName',
                                    'Relation' => 'Relation',
                                    'HomeAddressStreet' => 'HomeAddressStreet',
                                    'HomeAddressCityCode' => 'HomeAddressCityCode',
                                    'HomeAddressCityDesc' => 'HomeAddressCityDesc',
                                    'HomePhone' => 'HomePhone',
                                    'MobilePhone' => 'MobilePhone',
                                    'BillingHeader' => 'BillingHeader',
                                    'BillNumber' => 'BillNumber',
                                    'EpisodeType' => 'EpisodeType',
                                    'EpisodeNumber' => 'EpisodeNumber',
                                    'BillingItem' => 'BillingItem',
                                    'LocationCode' => 'LocationCode',
                                    'LocationDesc' => 'LocationDesc',
                                    'ItemCode' => 'ItemCode',
                                    'ItemDesc' => 'ItemDesc',
                                    'ItemGroupCode' => 'ItemGroupCode',
                                    'ItemGroupDesc' => 'ItemGroupDesc',
                                    'ItemUnityOfMeasureCode' => 'ItemUnityOfMeasureCode',
                                    'ItemUnityOfMeasureDesc' => 'ItemUnityOfMeasureDesc',
                                    'Insurance1PayorCode' => 'Insurance1PayorCode',
                                    'Insurance2PayorCode' => 'Insurance2PayorCode',
                                    'Insurance3PayorCode' => 'Insurance3PayorCode',
                                    'Insurance4PayorCode' => 'Insurance4PayorCode',
                                    'Numeric' => 'Numeric',
                                    'PatientBillingPaidEvent' => 'PatientBillingPaidEvent',
                                    'BillNumber' => 'BillNumber',
                                    'EpisodeNumber' => 'EpisodeNumber',
                                    'PatientEvent' => 'PatientEvent',
                                    'PatientMovementEvent' => 'PatientMovementEvent',
                                    'MovInfo' => 'MovInfo',
                                    'PatientPreAdmEvent' => 'PatientPreAdmEvent',
                                    'PatientUpdEvent' => 'PatientUpdEvent',
                                    'ProcedureReferralEvent' => 'ProcedureReferralEvent',
                                    'CareProvider' => 'CareProvider',
                                    'GivenName' => 'GivenName',
                                    'FamilyName' => 'FamilyName',
                                    'SecondaryName' => 'SecondaryName',
                                    'HomeAddressStreet' => 'HomeAddressStreet',
                                    'HomeAddressCityCode' => 'HomeAddressCityCode',
                                    'HomeAddressCityDesc' => 'HomeAddressCityDesc',
                                    'HomePhone' => 'HomePhone',
                                    'MobilePhone' => 'MobilePhone',
                                    'WorkPhone' => 'WorkPhone',
                                    'EMail' => 'EMail',
                                    'ProcedureReferralInfo' => 'ProcedureReferralInfo',
                                    'ReferralReasonCode' => 'ReferralReasonCode',
                                    'OriginatingRegionCode' => 'OriginatingRegionCode',
                                    'OriginatingSpecialtyCode' => 'OriginatingSpecialtyCode',
                                    'OriginatingFacilityCode' => 'OriginatingFacilityCode',
                                    'DiagnosisCode' => 'DiagnosisCode',
                                    'DiagnosisRemarks' => 'DiagnosisRemarks',
                                    'EpisodePriorityCode' => 'EpisodePriorityCode',
                                    'EpisodeComplexityLevelCode' => 'EpisodeComplexityLevelCode',
                                    'Remarks' => 'Remarks',
                                    'GeneralData' => 'GeneralData',
                                    'RequestedProcedureCode' => 'RequestedProcedureCode',
                                    'ReferralDischargeEvent' => 'ReferralDischargeEvent',
                                    'ReferralDocumentNumber' => 'ReferralDocumentNumber',
                                    'ReasonCode' => 'ReasonCode',
                                    'ReferralEvent' => 'ReferralEvent',
                                    'ReferralResponseEvent' => 'ReferralResponseEvent',
                                    'ReferralDocumentNumber' => 'ReferralDocumentNumber',
                                    'DestinationRegionCode' => 'DestinationRegionCode',
                                    'DestinationFacilityCode' => 'DestinationFacilityCode',
                                    'DestinationLocationCode' => 'DestinationLocationCode',
                                    'DestinationLocationDescription' => 'DestinationLocationDescription',
                                    'DestinationSpecialtyCode' => 'DestinationSpecialtyCode',
                                    'ResponseEvent' => 'ResponseEvent',
                                    'ErrorDescription' => 'ErrorDescription',
                                    'SendConfigEvent' => 'SendConfigEvent',
                                    'ConfigCode' => 'ConfigCode',
                                    'ConfigValue' => 'ConfigValue',
                                    'SpecialtyReferralEvent' => 'SpecialtyReferralEvent',
                                    'SpecialtyReferralInfo' => 'SpecialtyReferralInfo',
                                    'ReferralReasonCode' => 'ReferralReasonCode',
                                    'OriginatingRegionCode' => 'OriginatingRegionCode',
                                    'OriginatingSpecialtyCode' => 'OriginatingSpecialtyCode',
                                    'DiagnosisCode' => 'DiagnosisCode',
                                    'DiagnosisRemarks' => 'DiagnosisRemarks',
                                    'EpisodePriorityCode' => 'EpisodePriorityCode',
                                    'EpisodeComplexityLevelCode' => 'EpisodeComplexityLevelCode',
                                    'Remarks' => 'Remarks',
                                    'GeneralData' => 'GeneralData',
                                    'RequestedSpecialtyCode' => 'RequestedSpecialtyCode',
                                    'WLEntryEvent' => 'WLEntryEvent',
                                    'WLEntryCode' => 'WLEntryCode',
                                    'WLEntry' => 'WLEntry',
                                    'DiagnosisCode' => 'DiagnosisCode',
                                    'DiagnosisCodingSystem' => 'DiagnosisCodingSystem',
                                    'Remarks' => 'Remarks',
                                    'DiagnosisRemarks' => 'DiagnosisRemarks',
                                    'ReferralReasonCode' => 'ReferralReasonCode',
                                    'GeneralData' => 'GeneralData',
                                    'OriginatingRegionCode' => 'OriginatingRegionCode',
                                    'SpecialtyCode' => 'SpecialtyCode',
                                    'LocationCode' => 'LocationCode',
                                    'FacilityCode' => 'FacilityCode',
                                    'EpisodeComplexityLevelCode' => 'EpisodeComplexityLevelCode',
                                    'EpisodePriorityCode' => 'EpisodePriorityCode',
                                    'WLStatusUpdEvent' => 'WLStatusUpdEvent',
                                    'WLEntryCode' => 'WLEntryCode',
                                    'StatusCode' => 'StatusCode',
                                    'ReasonCode' => 'ReasonCode',
                                    'SendEventResponse' => 'SendEventResponse',
                                    'SendQuery' => 'SendQuery',
                                    'InsuranceQueryReq' => 'InsuranceQueryReq',
                                    'Query' => 'Query',
                                    'Message' => 'Message',
                                    'ControlID' => 'ControlID',
                                    'CorrelatedControlID' => 'CorrelatedControlID',
                                    'anonymous247' => 'anonymous247',
                                    'anonymous248' => 'anonymous248',
                                    'anonymous249' => 'anonymous249',
                                    'anonymous250' => 'anonymous250',
                                    'InsuranceQueryRes' => 'InsuranceQueryRes',
                                    'ErrorMessage' => 'ErrorMessage',
                                    'SendQueryResponse' => 'SendQueryResponse',
                                    'TestConnection' => 'TestConnection',
                                    'TestConnectionResponse' => 'TestConnectionResponse',
                                   );

  public function InterSystems($wsdl = "http://10.8.163.80/sdvn/sdvnint/SMGES.BS.WSService.CLS?WSDL=1", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param SendEvent $parameters
   * @return SendEventResponse
   */
  public function SendEvent(SendEvent $parameters) {
    return $this->__soapCall('SendEvent', array($parameters),       array(
            'uri' => 'InterSystems',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SendQuery $parameters
   * @return SendQueryResponse
   */
  public function SendQuery(SendQuery $parameters) {
    return $this->__soapCall('SendQuery', array($parameters),       array(
            'uri' => 'InterSystems',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param TestConnection $parameters
   * @return TestConnectionResponse
   */
  public function TestConnection(TestConnection $parameters) {
    return $this->__soapCall('TestConnection', array($parameters),       array(
            'uri' => 'InterSystems',
            'soapaction' => ''
           )
      );
  }

}

?>
