<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Firebase extends BaseConfig
{
    public array $credentials = [
        'type' => 'service_account',
        'project_id' => 'playground-ed15c',
        'private_key_id' => 'cf6f34c5224b9e6c81eadb22d868a7ef456eaffe',
        'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCepdV4hAs8PC2Y\nYvTmkoV9BWD3Su1KbonGrFHE1/1CvhYhz9aGPS9HRTXNzGavLPDbw0xpH6xyyO2Q\nFUk5yATRoBWVIl5PFdtlZNZOdA9qhOtQlxZ5TovTENsjjdASpMCF/Ui7cLTB/fvV\nBDWCIX4mH4FWVdfvEByozsto9zw9W59j9bpJLg9pb/Dbgj6Gdavt8Kvc2SPo9ZzY\nuFe2uUWRU3ovjky7tU5ytDYm5VlMWRDY4PRmxgBhI6PjAsryrj0Pr72SEacg/uVa\nmI600SDZHYBs72LPXwev0BEjvNXf0KlnJVZEW7qpO6UrxNH3dzy4hGFcWFEp9GfL\nk/ciI+QfAgMBAAECggEAAv+3K3oyLNcltNSOCju+epCwTAxmXuVRr707JcxAFSkQ\nXwmVkF+2Cz6uLS/jcwVCSk0vSRTDBupwadb/1YVH6LSCI+8MjFR+m185boM4IAyC\nLDmm6u/GqdyO3+Wth2Sw36IxjprV9+LMBgijKvdkxztfj65mELajwWHOAN5yN5Ct\nAmHAj2IPDhI70PRLmTwFQaZQOcV+Pe/YuO9nN8eGCKykVEh3RVR6a1H9tVVtYp/k\nvGfu95nis5UGkmQNsYq9PUzzw8rSxuZ6lFLuHN0sZL+7X+MCkPXUK9AbSDhn3yFf\ngFiCvYKo/CH5tJpDlqEktQ/GND7LUVDzTOTfRohlJQKBgQDYax6+TCz+0n4/Tt5Z\nQQ8D9OAV+FaxLGuGLd/JnLVF8nVThC9vyEmFVHba9ZxWX761AOEx/iIBjypRlmru\nGXmwasGIiQGsjSzyxClOadZBsUmtqdi6LvDUAn/Rapwa/trZM6F8mElpddmuFoRb\nqmFpmGCYwFrPXxjF3b9t/w213QKBgQC7qdjSWwm46Q+K78XX76/p/t7yhXnensOb\nRqGOPHDwh2lcYOHvNlt6W9a3UvDDRDtvCM0oRzHCy9S2m9Dp/DYTVbYFNxeB0MMk\nZUkI4qr79txforzhdeIRleezaW1Oy6AHKIafSlBqUQtp9kuvUq+Iwm37yN4D0Z8y\n7GHqWWY4KwKBgQC9wm9afICXjwmTiRv9NsZrubMikHWzNDezl1W5+eYYRtgJOsY9\nRWjxt3Xf16wVMC6Plw3eP+6hX17D2xg3Xa8NuQ2pIjiNi9v1oXp2fuaXA1SPKXYX\nCkGwI+DfRpNKOFy8RkE6dm0oQ2Vy99tmZKa1aB6K0V7OpZubIyxOzmy/IQKBgQCX\nd18paZFPTczdtulYgHzbwHryBAl5RWtbyIZc5yvA94pypT+2c4kiGT4UeT0aXdga\npl/wQoZHU308ZkcYlBiRQnzHeM/gVz8qARYRteGLvJPiHNeWoi71QqjghhdTocZo\nAK2no7OEHCW6QLCVsYc8OrHKpzWKn4Z+84uUOtPmQQKBgCOmvG6DRcmuhJzKttcY\nkWLnizD+MGIeLryNxPeCYupbjULAMSnUu4NRtPUAZP8cbxj9qm/eq98yOT635GL3\n9TEEWQkYk8jcFsuT46txAtMP5pgX6VdQpjBiYN3FU1N+5k5JJJMWcFi7tL5WBA23\n+QRgajjULLBgSh9CN4SithA4\n-----END PRIVATE KEY-----\n",
        'client_email' => 'firebase-adminsdk-fbsvc@playground-ed15c.iam.gserviceaccount.com',
        'client_id' => '100302969772102766485',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40playground-ed15c.iam.gserviceaccount.com',
        'universe_domain' => 'googleapis.com',
    ];
}
