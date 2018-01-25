<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;

class ProfileListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'userDOB' => 'required|max:255',
            //'userMobile' => 'required|max:255',
            //'userSmoke' => 'required|max:255',
            //'userReligion' => 'required|max:255',
            //'userState' => 'required|max:255',
        ];
    }
}
