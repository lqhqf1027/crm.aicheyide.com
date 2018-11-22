const app = getApp()
const vehicle_configuration = {
    "基本参数": {
        "img": "https://car3.autoimg.cn/cardfs/product/g23/M08/A5/FF/t_autohomecar__wKgFV1n_EbSAThpVAA2qPOYq89c808.jpg",
        "manufacturers": "一汽-大众",
        "level": "紧凑型车",
        "engine": "1.6L 110马力 L4",
        "gearbox": "5挡手动",
        "longHighWith": "4655*1780*1453",
        "bodyStructure": "4门5座三厢车",
        "maximumSpeed": "187",
        "officialAcceleration": "12.5",
        "measuredAcceleration": "-",
        "measuredBrake": "-",
        "measuredFuelConsumption": "-",
        "averageConsumptionOfCertification": "7.3(16人平均值)",
        "ministryOfIntegratedFuelConsumption": "6.5",
        "measuredGroundClearance": "",
        "vehicleQuality": "三年或10万公里"
    },
    "车身": {
        "length": "4655",
        "width": "1780",
        "height": "1453",
        "wheelbase": "2651",
        "frontTrack": "-",
        "rearWheel": "-",
        "minnumGroundClearance": "113",
        "kerbMass": "1300",
        "bodyStructure": "三厢车",
        "numberOfDoors": "4",
        "numberOfSeats": "5",
        "mailVolume": "-",
        "compartmentVolume": "510"
    },
    "发动机": {
        "model": "EA211",
        "displacement": "1598",
        "intakeForm": "自然吸气",
        "cylinderExhaustForm": "L",
        "cylinders": "4",
        "valvePerCylinder": "4",
        "compressionRatio": "-",
        "gasDistrbutionMechanism": "DOHC",
        "cylinderBy": "-",
        "trip": "-",
        "maxHorsepower": "110",
        "maxPower": "81",
        "maxPowerSpeed": "5800",
        "maxTorque": "155",
        "maxTorqueSpeed": "3800",
        "specialTechnology": "-",
        "fuelForm": "汽油",
        "fuel": "92号",
        "fuleWay": "多点电喷",
        "cylinderHeadMeterial": "铝合金",
        "cylinderMaterial": "铝合金",
        "environmentalProtection": "国V"
    },
    "变速箱": {
        "abbreviation": "5挡手动",
        "grarNum": "5",
        "type": "手动变速箱(MT)"
    },
    "底盘转向": {
        "drivingMethod": "前置前驱",
        "fourWheelDriveForm": "",
        "centralDifferentialStructure": "",
        "frontSuspensionType": "麦弗逊式独立悬架",
        "SuspensionType": "多连杆独立悬架",
        "powerType": "电动助力",
        "bodyStructure": "承载式"
    },
    "车轮制动": {
        "frontBrakeType": "通风盘式",
        "brakeType": "盘式",
        "parkingBrakeType": "手刹",
        "frontTyreSpecifications": "205/55 R16",
        "typreSpecifications": "205/55 R16",
        "spareTrieSpecifications": "非全尺寸"
    },
    "安全装备": {
        "lordDeputyDirversSeatAirbag": "主● / 副●",
        "frontAndRearSideAirbags": "前● / 后-",
        "beforeAndAffterTheHeadAirbag": "前○ / 后○",
        "kneeAirbag": "-",
        "pressureMonitoringDevice": "●",
        "zeroPressureContinuedTravel": "-",
        "sagetyBeltPrompt": "●",
        "childSeatInterface": "●",
        "engineElectronicAntitheft": "●",
        "controlLock": "●",
        "RmeoteKey": "-",
        "keylessStartSystem": "●",
        "keylessEntrySystem": "●"
    },
    "操控配置": {
        "ABS": "●",
        "brakingForceDistribution": "●",
        "braleAssist": "●",
        "tractionControl": "●",
        "stabilityControl": "●",
        "upslopeAuxiliary": "●",
        "automaticParking": "-",
        "steepSlopeSlowlyDescending": "-",
        "variableSuspension": "-",
        "frontAxleLimitedSlip": "-",
        "centralDifferential": "-",
        "axleLimitedSlip": "-",
        "differentialLocking": "-",
        "DifferetialMechanism": "-"
    },
    "外部配置": {
        "electricSkylight": "●",
        "panoramicSunroof": "-",
        "appearancePackage": "-",
        "aluminumAlloyWheels": "●",
        "electricSuctionDoor": "-",
        "slideDoor": "-",
        "electricTrunk": "-"
    },
    "内部配置": {
        "leatherSteeringWheel": "",
        "steeringWheelAdjustment": "上下+前后调节",
        "steeringWheelOfElectricControl": "-",
        "multifunctionSteeringWheel": "●",
        "steeringWheelShift": "-",
        "steeringWheelHeating": "-",
        "cruiseControl": "●",
        "parkingRadar": "前- / 后●",
        "reverseVideoPhotography": "-",
        "drivingComputerDispaly": "●",
        "HUD": "-"
    },
    "座椅配置": {
        "genuineLeather": "",
        "movementStyle": "-",
        "heightAdjustment": "●",
        "lumbarSupport": "●",
        "shoulderSupport": "-",
        "driverSeatElectricAdjustment": "-",
        "SecondRowOfBackrestAngleAdjustment": "-",
        "secondSeatMove": "-",
        "RearSeatElectricAdjustment": "-",
        "electricSeatMemory": "-",
        "Heating": "前○ / 后-",
        "ventilation": "-",
        "massage": "-",
        "backDowmMode": "比例放倒",
        "ThirdRowSeat": "-",
        "handrail": "前● / 后●",
        "rearGlassFrame": "●"
    },
    "多媒体配置": {
        "GPS": "-",
        "orientationOfInteraction": "",
        "consoleScreen": "●",
        "hardDrive": "",
        "bluetoothPhone": "●",
        "TV": "-",
        "rearLcdScreen": "-",
        "externalSoundSource": "USB+AUX+SD卡插槽",
        "cd": "-",
        "multimediaSystem": "",
        "speakerBrand": "-",
        "loundspeakersNum": "8-9喇叭"
    },
    "灯光配置": {
        "gangGasHeadlight": "卤素",
        "LED": "卤素",
        "daytimeWalkLamp": "-",
        "automaticHeadlights": "-",
        "steeringSuxiliaryLamp": "●",
        "steeringHeadlamp": "-",
        "frontFogLamp": "●",
        "headlightAdjusting": "●",
        "headlightCleaning": "-",
        "atmosphereLamp": "-"
    },
    "玻璃后视镜": {
        "electricWindow": "前● / 后●",
        "preventClampsHand": "●",
        "ultravioletRays": "-",
        "mirrorElectricAdiustment": "●",
        "rearviewMirrorHeating": "●",
        "antiGtlare": "内● / 外-",
        "fold": "-",
        "memory": "-",
        "yangCurtain": "-",
        "ceZheCurtain": "-",
        "privacyglass": "-",
        "cosmeticMirror": "●",
        "rearWiper": "-",
        "sensingWipers": "●"
    },
    "空调冰箱": {
        "controlMethod": "手动●",
        "rearAirConditioning": "",
        "rearSeatVents": "●",
        "temperatureCXontrol": "-",
        "airConditioning": "-",
        "regrigerator": "-"
    },
    "高科技配置": {
        "automaticParking": "-",
        "engineStartStop": "-",
        "auxiliary": "-",
        "laneDepartureWarning": "-",
        "activesafety": "-",
        "activeSteering": "-",
        "nightVisionSystem": "-",
        "lcdScreen": "-",
        "adaptiveCruiseControl": "-",
        "panoramicCamera": "-"
    }
}

Page({
    data: {
        vehicle_configuration,
    },
    onLoad(options) {
        console.log(options)
        this.options = options
            // this.getDetail()
    },
    getDetail() {
        const plan_id = this.options.id

        app.request('/index/plan_details', { plan_id }, (data, ret) => {
            console.log(data)
            this.setData({
                vehicle_configuration: data.plan.models.vehicle_configuration,
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
})