<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
use App\Notifications\Mentioned;
use App\Reply;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
    }

    protected function failedAuthorization()
    {
        throw new ThrottleException('Error! Please submit your reply later');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|blockspam'
        ];
    }
}
