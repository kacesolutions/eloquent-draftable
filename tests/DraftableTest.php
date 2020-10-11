<?php

namespace Kace\Draftable\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DraftableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_a_model_is_draft(): void
    {
        $draft = factory(TestModel::class)->make();

        $this->assertTrue($draft->isDraft());

        $scheduled = factory(TestModel::class)
            ->state('scheduled')
            ->make();

        $this->assertTrue($scheduled->isDraft());
    }

    /** @test */
    public function it_can_determine_if_a_model_is_published(): void
    {
        $published = factory(TestModel::class)
            ->state('published')
            ->make();

        $this->assertTrue($published->isPublished());

        $scheduled = factory(TestModel::class)
            ->state('scheduled')
            ->make();

        $this->assertFalse($scheduled->isPublished());

        Carbon::setTestNow(Carbon::now()->addDay());

        $this->assertTrue($scheduled->isPublished());
    }

    /** @test */
    public function it_can_retrieves_published_models_by_default()
    {
        factory(TestModel::class)->create();

        factory(TestModel::class)
            ->state('published')
            ->create();

        $models = TestModel::all();

        $this->assertCount(1, $models);
        $this->assertTrue(
            $models
                ->first()
                ->isPublished()
        );
    }

    /** @test */
    public function it_can_retrives_models_with_drafts()
    {
        factory(TestModel::class)->create();

        factory(TestModel::class)
            ->state('published')
            ->create();

        $models = TestModel::withDrafts()->get();
        $draftModel = TestModel::onlyDrafts()->get();
        $publishedModel = TestModel::all();

        $this->assertCount(2, $models);
        $this->assertCount(1, $draftModel);
        $this->assertCount(1, $publishedModel);
    }

    /** @test */
    public function it_can_retrives_draft_models_only()
    {
        factory(TestModel::class)->create();

        factory(TestModel::class)
            ->state('published')
            ->create();

        $models = TestModel::onlyDrafts()->get();

        $this->assertCount(1, $models);
        $this->assertTrue(
            $models
                ->first()
                ->isDraft()
        );
    }

    /** @test */
    public function it_can_mark_a_model_as_published()
    {
        $model = factory(TestModel::class)->make();

        $this->assertFalse($model->isPublished());

        $model->publish();

        $this->assertTrue($model->isPublished());
    }

    /** @test */
    public function it_can_mark_a_model_as_published_without_saving()
    {
        $model = factory(TestModel::class)->make();

        $this->assertFalse($model->isPublished());

        $model->setPublished(true);

        $this->assertTrue($model->isPublished());

        $this->assertTrue($model->isDirty());
    }

    /** @test */
    public function it_can_mark_a_model_as_draft()
    {
        $model = factory(TestModel::class)
            ->state('published')
            ->make();

        $this->assertFalse($model->isDraft());

        $model->draft();

        $this->assertTrue($model->isDraft());
    }

    /** @test */
    public function it_can_mark_a_model_as_draft_without_saving()
    {
        $model = factory(TestModel::class)
            ->state('published')
            ->make();

        $this->assertFalse($model->isDraft());

        $model->setPublished(false);

        $this->assertTrue($model->isDraft());

        $this->assertTrue($model->isDirty());
    }

    /** @test */
    public function it_can_publish_or_draft_a_model_based_on_a_boolean_value()
    {
        $model = factory(TestModel::class)
            ->make();

        $this->assertTrue($model->isDraft());

        $model->publish(true);

        $this->assertTrue($model->isPublished());

        $model->publish(false);

        $this->assertTrue($model->isDraft());
    }

    /** @test */
    public function it_can_schedule_a_model_to_be_published()
    {
        $model = factory(TestModel::class)->make();

        $model->publishAt($scheduleAt = Carbon::now()->addDays(7));

        $this->assertTrue($model->isDraft());

        Carbon::setTestNow($scheduleAt);

        $this->assertTrue($model->isPublished());
    }

    /** @test */
    public function it_can_schedule_a_model_to_be_published_without_saving()
    {
        $model = factory(TestModel::class)->make();

        $model->setPublishedAt($scheduleAt = Carbon::now()->addDays(7));

        $this->assertTrue($model->isDraft());
        $this->assertTrue($model->isDirty());

        Carbon::setTestNow($scheduleAt);

        $this->assertTrue($model->isPublished());
        $this->assertTrue($model->isDirty());
    }

    /** @test */
    public function it_can_accept_a_null_publish_date_to_indefinitely_draft_a_model()
    {
        $model = factory(TestModel::class)
            ->state('published')
            ->create();

        $model->setPublishedAt(null);

        $this->assertTrue($model->isDraft());
        $this->assertTrue($model->isDirty());

        $model->setPublished(true);

        $model->publishAt(null);

        $this->assertTrue($model->isDraft());
        $this->assertTrue($model->isClean());
    }

    /** @test */
    public function it_does_not_change_published_at_timestamp_when_publishing_model_an_already_published()
    {
        $model = factory(TestModel::class)->make([
            'published_at' => $publishedAt = Carbon::now(),
        ]);

        $model->setPublished(true);

        $this->assertSame(
            $publishedAt->toDateString(),
            $model->published_at->toDateString()
        );
    }
}
